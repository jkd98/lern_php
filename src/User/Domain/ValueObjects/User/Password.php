<?php

namespace Domain\ValueObjects\Shared;

readonly class Password {
    private string $hash;

    // EL GUARDUÁN: El constructor es privado. 
    // Nadie puede hacer 'new Password()' desde fuera.
    private function __construct(string $hash) {
        $this->hash = $hash;
    }

    // LA PUERTA PARA EL REGISTRO:
    public static function create(string $plainPassword): self {
        // 1. Valida el negocio: ¿Es segura?
        if (strlen($plainPassword) < 8) {
            throw new InvalidArgumentException("Muy corta");
        }

        // 2. Transforma el dato: Lo hashea.
        $hashedPassword = password_hash($plainPassword, PASSWORD_BCRYPT);
        
        // 3. Retorna el objeto usando el constructor privado.
        return new self($hashedPassword);
    }

    // LA PUERTA PARA LA BASE DE DATOS:
    public static function fromHash(string $hash): self {
        // Aquí no validas longitud ni hasheas, porque ya viene listo de la DB.
        return new self($hash);
    }

    // EL VERIFICADOR:
    public function verify(string $plainPassword): bool {
        // Compara el texto que mete el usuario en el Login con el hash que guardamos.
        return password_verify($plainPassword, $this->hash);
    }
}
