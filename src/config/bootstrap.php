<?php
declare(strict_types=1);

require_once __DIR__.'/db.php';

use App\User\Infraestructure\Persistence\Pdo\PdoUserRepository;
use App\User\Application\Services\RegisterUser\RegisterUserService;
use App\User\Http\Controllers\RegisterUserController;

/**
 * Deveulve la instancia PDO recén creada
 */
function getPDO() : PDO{
    return conectar();
}

/**
 * Construye y devuelve el controlador para el caso de uso "Registrar usuario".
 */
function createRegisterUserController(PDO $pdo):RegisterUserController{
    $userRepository = new PdoUserRepository($pdo);
    $registerUserService = new RegisterUserService($userRepository);
    $registerUserController = new RegisterUserController($registerUserService);
    return $registerUserController;
}