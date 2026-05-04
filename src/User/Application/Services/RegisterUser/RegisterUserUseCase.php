<?php
namespace App\User\Application\Services\RegisterUser;

use App\User\Application\Services\RegisterUser\RegisterUserRequestDTO;
use App\User\Application\Services\RegisterUser\RegisterUserResponseDTO;

interface RegisterUserUseCase {
    public function execute(RegisterUserRequestDTO $request): RegisterUserResponseDTO;
}