<?php

namespace Domain\ValueObjects;

use Ramsey\Uuid\Uuid;
use InvalidArgumentException;

readonly class UUIDv7 {
    private string $value;

    /**
     * El constructor es privado para forzar el uso de métodos estáticos
     * (Named Constructors), lo que hace el código más legible.
     */
    private function __construct(?string $value = null) {
        if ($value === null) {
            // Generamos un UUIDv7 nuevo
            $this->value = Uuid::uuid7()->toString();
        } else {
            // Si nos dan uno, validamos que sea un UUID válido
            if (!Uuid::isValid($value)) {
                throw new InvalidArgumentException("El ID '$value' no es un UUID válido.");
            }
            $this->value = $value;
        }
    }

    // Para crear un ID nuevo 
    public static function generate(): self {
        return new self();
    }

    // Para crear un objeto ID desde un string (cuando viene de la DB o URL)
    public static function fromString(string $id): self {
        return new self($id);
    }

    public function getValue(): string {
        return $this->value;
    }

    // Método útil para comparaciones rápidas
    public function equals(UUIDv7 $other): bool {
        return $this->value === $other->getValue();
    }
}