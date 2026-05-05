<?php

declare(strict_types=1);

namespace App\Infraestructure\Router;

use App\User\Http\Routes\RouteHandlerInterface;

/**
 * Router minimalista para matchear rutas HTTP
 *
 * Responsabilidades:
 * - Registrar rutas [METHOD, PATH, HANDLER]
 * - Matchear método + path contra petición actual
 * - Retornar handler si coincide
 *
 * Nota: Este es un router muy simple (match exacto).
 * En producción usarías Symfony Routing, FastRoute, etc.
 */
class Router
{
    /**
     * Almacena las rutas registradas
     * Formato: [
     *   [method => 'POST', path => '/api/users/register', handler => object],
     *   ...
     * ]
     */
    private array $routes = [];

    /**
     * Registra una nueva ruta
     *
     * @param string $method Verbo HTTP (GET, POST, PUT, DELETE, PATCH)
     * @param string $path Path exacto de la ruta (ej: /api/users/register)
     * @param RouteHandlerInterface $handler Objeto invocable que implementa RouteHandlerInterface
     * @return void
     */
    public function register(string $method, string $path, RouteHandlerInterface $handler): void
    {
        $this->routes[] = [
            'method' => strtoupper($method),
            'path' => $path,
            'handler' => $handler,
        ];
    }

    /**
     * Busca una ruta que coincida con el método y path
     *
     * @param string $method Método HTTP de la petición actual
     * @param string $path Path de la petición actual
     * @return RouteHandlerInterface|null Handler si hay coincidencia, null en caso contrario
     */
    public function match(string $method, string $path): ?RouteHandlerInterface
    {
        $method = strtoupper($method);

        foreach ($this->routes as $route) {
            if ($route['method'] === $method && $route['path'] === $path) {
                return $route['handler'];
            }
        }

        return null;
    }

    /**
     * Retorna todas las rutas registradas
     *
     * Útil para debugging o generar documentación.
     *
     * @return array Array de rutas
     */
    public function getRoutes(): array
    {
        return $this->routes;
    }

    /**
     * Cuenta las rutas registradas
     *
     * @return int Cantidad de rutas
     */
    public function count(): int
    {
        return count($this->routes);
    }
}
