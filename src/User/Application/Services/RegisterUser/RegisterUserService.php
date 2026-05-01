<?php

namespace App\User\Application\Services\RegisterUser;

use App\User\Application\Services\RegisterUser\RegisterUserRequestDTO;
use App\User\Application\Services\RegisterUserRegisterUserResponseDTO;
use App\User\Application\Exceptions\EmailAlreadyRegisteredException;

use App\User\Domain\IUserRepository;

use App\User\Domain\ValueObjects\Email;
use App\User\Domain\ValueObjects\Password;
use App\User\Domain\ValueObjects\UserName;
use App\Shared\Domain\ValueObjects\UUIDv7;

use App\User\Domain\Entities\User;


interface RegisterUserUseCase {
    public function execute(RegisterUserRequestDTO $request): RegisterUserResponseDTO;
}

final class RegisterUserService implements RegisterUserUseCase {
    public function __construct(private IUserRepository $repository){}

    public function execute(RegisterUserRequestDTO $request): RegisterUserResponseDTO{
        // Validar el email
        $email = Email::create($request->email);

        $user_exists = $this->repository->findByEmail($email);
        if($user_exists){
            throw new EmailAlreadyRegisteredException();
        }

        // crear al usuario
        $nw_user = new User(
            id:UUIDv7::generate(),
            name:UserName::create($request->name),
            email:Email::create($request->email),
            password:Password::create($request->password)
        );

        // guardarlo
        $this->repository->save($nw_user);

        //repuesta
        return new RegisterUserResponseDTO(
            id:$nw_user->id->getValue(),
            name:$nw_user->name->getValue(),
            email:$nw_user->email->getValue()
        );
    }
}

?>