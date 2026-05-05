<?php 
declare(strict_types=1);

namespace App\User\Infraestructure\Persistence\Eloquent;

use App\User\Domain\IUserRepository;
use App\User\Domain\Entities\User;

final class PdoUserRepository implements IUserRepository{
    public function __construct(private $pdo) { }
    
    public function save(User $user):User{
        try {
            $stmt = $pdo->prepare("INSERT INTO users(id,name,email,password) VALUES(:id,:name,:email,:password)");
            $stmt->execute([
                'id'=>$user->id,
                'name'=>$user->name,
                'email'=>$user->email,
                'password'=>$user->password
            ]);
        } catch (\Throwable $th) {
            throw $th;
        }
    }
}