<?php

namespace App\User\Application\Services\RegisterUser;

final class RegisterUserRequestDTO {
    public function __construct(
        private readonly string $name,
        private readonly string $email,
        private readonly string $password
    ) { }
}

?>