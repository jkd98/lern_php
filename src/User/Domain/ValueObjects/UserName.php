<?php

namespace App\User\Domain\ValueObjects;

use InvalidArgumentException;

readonly class UserName {
    private string $user_name;

    private function __contruct(string $user_name){
        if(strlen($user_name) < 3){
            throw new InvalidArgumentException("El nombre de usuario '$user_name' es demasiado corto.");
        }
        $this->user_name = $user_name;
    }

    public static function create (string $user_name):self {
        return new self($user_name);
    }
}

?>