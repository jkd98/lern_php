<?php

declare(strict_types=1);

namespace App\User\Http\Routes;

use App\User\Http\Controllers\RegisterUserController;

/**
 * Handler invocable para la ruta POST /api/users/register
 *
 * Separa la responsabilidad HTTP (lectura de request) de la lógica de aplicación.
 * Al implementar __invoke(), la instancia puede usarse como función.
 */
class RegisterUserHandler implements RouteHandlerInterface
{
    public function __construct(
        private RegisterUserController $controller
    ) {}

    /**
     * Procesa la petición de registro de usuario
     *
     * @return string JSON con la respuesta
     */
    public function __invoke(): string
    {
        // Leer body del request
        // php://input trae el contenido crudo del POST (raw body)
        $rawBody = file_get_contents('php://input');

        // Parsear JSON a array asociativo
        // - true => array (no objeto stdClass)
        // - ?? [] => si viene null/invalid, usamos array vacío
        $data = json_decode($rawBody, true) ?? [];

        // Delegar al controlador
        // Aquí termina la responsabilidad HTTP
        // La lógica de caso de uso vive en Application/Domain
        return $this->controller->execute($data);
    }
}
