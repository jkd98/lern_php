<?php

declare(strict_types=1);

namespace App\User\Http\Routes;

/**
 * Handler invocable para la ruta GET api/users/hello
 *
 * Ruta de prueba que no requiere dependencias externas.
 * Al implementar __invoke(), la instancia puede usarse como función.
 *
 * Responsabilidades:
 * - Validar parámetros del request
 * - Retornar respuesta JSON
 */
class HelloHandler implements RouteHandlerInterface
{
    /**
     * Retorna un mensaje de prueba
     *
     * @return string JSON con el mensaje
     * @throws \JsonException
     */
    public function __invoke(): string
    {
        return json_encode([
            'status' => 'success',
            'msg' => 'Hola desde la ruta de prueba'
        ], JSON_THROW_ON_ERROR);
    }
}
