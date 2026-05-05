<?php

declare(strict_types=1);

namespace App\Config;

use App\Infraestructure\Container\Container;
use App\User\Infraestructure\Factories\RegisterUserControllerFactory;
use App\User\Http\Routes\RegisterUserHandler;
use App\User\Http\Routes\HelloHandler;

/**
 * ServicesBootstrapper: Configuración de dependencias
 *
 * Responsabilidad:
 * - Registrar todas las factories de servicios en el contenedor
 * - Definir cómo se construyen y sus dependencias
 *
 * Patrón: Service Configuration / Bootstrapper
 */
class ServicesBootstrapper
{
    /**
     * Bootea todos los servicios en el contenedor
     *
     * @param Container $container Contenedor donde registrar servicios
     * @return void
     */
    public function __invoke(Container $container): void
    {
        // ====== Infraestructure Services ======

        /**
         * Servicio: Conexión a Base de Datos
         *
         * Factory que retorna una instancia PDO.
         */
        $container->register('pdo', static function (Container $container) {
            require_once __DIR__ . '/db.php';
            return conectar();
        });

        // ====== Application Services ======

        /**
         * Servicio: Controlador de Registro de Usuario
         *
         * Depende de:
         * - pdo: conexión a base de datos
         *
         * Se construye vía Factory dedicada.
         */
        $container->register('register_user_controller', static function (Container $container) {
            $pdo = $container->get('pdo');
            return RegisterUserControllerFactory::create($pdo);
        });

        // ====== Presentation Layer - HTTP Handlers ======

        /**
         * Servicio: Handler HTTP para POST /api/users/register
         *
         * Responsabilidad:
         * - Leer body del request
         * - Parsear JSON
         * - Delegar al controlador
         *
         * Depende de:
         * - register_user_controller: controlador de aplicación
         */
        $container->register('register_user_handler', static function (Container $container) {
            $controller = $container->get('register_user_controller');
            return new RegisterUserHandler($controller);
        });

        /**
         * Servicio: Handler HTTP para GET /api/users/hello
         *
         * Responsabilidad:
         * - Retornar mensaje de prueba
         *
         * Dependencias: Ninguna
         */
        $container->register('hello_handler', static function (Container $container) {
            return new HelloHandler();
        });
    }
}
