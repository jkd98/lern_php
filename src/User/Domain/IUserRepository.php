<?php

namespace App\User\Domain;

use App\Shared\Domain\ValueObjects\UUIDv7;
use App\User\Domain\ValueObjects\Email;
use App\User\Domain\Entities\User;

interface IUserRepository {
    public function save(User $user): User;
    public function findByUUID(UUIDv7 $id): User | null;
    public function findByEmail(Email $email): User | null;
}


?>