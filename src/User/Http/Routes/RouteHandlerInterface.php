<?php

declare(strict_types=1);

namespace App\User\Http\Routes;

/**
 * Contrato para Route Handlers (objetos invocables)
 *
 * Todo handler HTTP debe implementar esta interfaz.
 *
 * Beneficios:
 * - Type-hinting claro
 * - Contrato explícito
 * - Facilita testing y mocking
 * - Mejor mantenibilidad
 */
interface RouteHandlerInterface
{
    /**
     * Ejecuta el handler
     *
     * Responsabilidades típicas:
     * - Procesar petición HTTP (request)
     * - Delegar a Application Layer si es necesario
     * - Retornar respuesta JSON
     *
     * @return string Respuesta JSON
     */
    public function __invoke(): string;
}
