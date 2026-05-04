<?php

declare(strict_types=1);

use App\User\Http\Controllers\RegisterUserController;

return function (PDO $pdo): array {
    return [
        [
            'POST', '/api/users/register', 
            function () use ($pdo) {
                $controller = createRegisterUserController($pdo);
                $data = json_decode(file_get_contents('php://input'), true) ?? [];
                return $controller->execute($data);
            }
        ],
        // otras rutas del módulo User...
    ];
};