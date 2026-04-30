<?php

namespace App\User\Domain\Entities;

use App\Shared\Domain\ValueObjects\UUIDv7;
use App\User\Domain\ValueObjects\UserName;
use App\User\Domain\ValueObjects\Email;
use App\User\Domain\ValueObjects\Password;

readonly class User {
    /**
     * Al poner 'readonly' en la clase y usar el constructor promocionado,
     * PHP hace 3 cosas por ti:
     * 1. Declara las propiedades.
     * 2. Recibe los parámetros.
     * 3. Asigna los valores.
     * Y garantiza que NUNCA cambien.
     */
    public function __construct(
        public UUIDv7 $id,
        public UserName $name,
        public Email $email,
        public Password $password
    ) {}

    public function canLoginWith(string $plainPassword): bool {
    return $this->password->verify($plainPassword);
}
}