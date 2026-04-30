<?php

namespace App\User\Domain\ValueObjects;

use InvalidArgumentException;


final readonly class Email {
    private string $value;

    private function __construct(string $email) {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new InvalidArgumentException("El email '$email' no es válido.");
        }
        $this->value = $email;
    }

    public static function create(string $email): self {
        return new self($email);
    }

    public function equals(Email $other): bool {
        return strtolower($this->value) === strtolower($other->getValue());
    }
}
?>