<?php
declare(strict_types=1);

namespace App\User\Infraestructure\Factories;

use PDO;
use App\User\Infraestructure\Persistence\Pdo\PdoUserRepository;
use App\User\Application\Services\RegisterUser\RegisterUserService;
use App\User\Http\Controllers\RegisterUserController;

/**
 * FACTORY (patrón de diseño):
 * Encapsula la construcción del controlador y su grafo de dependencias.
 *
 * Beneficio:
 * - Evita hacer "new" en routes/controllers.
 * - Centraliza el wiring técnico en Infrastructure.
 */
final class RegisterUserControllerFactory
{
    /**
     * Crea un RegisterUserController completamente armado.
     */
    public static function create(PDO $pdo): RegisterUserController
    {
        // Adapter de infraestructura que implementa IUserRepository.
        $userRepository = new PdoUserRepository($pdo);

        // Caso de uso (Application) que depende de contrato de dominio.
        $registerUserService = new RegisterUserService($userRepository);

        // Controller (Presentation) que invoca el caso de uso.
        return new RegisterUserController($registerUserService);
    }
}
