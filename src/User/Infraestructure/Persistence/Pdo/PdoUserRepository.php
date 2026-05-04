<?php 
declare(strict_types=1);

namespace App\User\Infraestructure\Persistence\Eloquent;

use App\User\Domain\IUserRepository;
use App\User\Domain\Entities\User;

final class PdoUserRepository implements IUserRepository{
    public function __construct(private $pdo) { }
    
    public function save(User $user):User{

    }
}