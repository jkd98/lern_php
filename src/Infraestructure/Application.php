<?php

declare(strict_types=1);

namespace App\Infraestructure;

use App\Infraestructure\Container\Container;
use App\Infraestructure\Router\Router;

/**
 * Kernel/Application: Orquestador principal de la aplicación
 *
 * Responsabilidades:
 * - Bootear el contenedor con todas las dependencias
 * - Bootear el router con todas las rutas
 * - Procesar la petición HTTP actual
 * - Ejecutar el handler y enviar respuesta
 *
 * Patrón: Front Controller + Service Locator
 */
class Application
{
    private Container $container;
    private Router $router;

    public function __construct()
    {
        $this->container = new Container();
        $this->router = new Router();
    }

    /**
     * Ejecuta la aplicación
     *
     * Flujo:
     * 1. Bootea servicios en el contenedor
     * 2. Registra todas las rutas
     * 3. Normaliza el path de la petición
     * 4. Busca handler que matchee
     * 5. Ejecuta handler o retorna 404
     *
     * @return void
     */
    public function run(): void
    {
        try {
            // 1) Bootear dependencias
            $this->bootServices();

            // 2) Registrar rutas
            $this->registerRoutes();

            // 3) Obtener método y path normalizados de la petición actual
            $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
            $path = $this->normalizePath();

            // 4) Buscar handler
            $handler = $this->router->match($method, $path);

            if ($handler === null) {
                $this->sendNotFound($method, $path);
                return;
            }

            // 5) Ejecutar handler
            $response = $handler();

            // 6) Enviar respuesta
            if (is_string($response)) {
                echo $response;
            }
        } catch (\Exception $e) {
            $this->sendError($e);
        }
    }

    /**
     * Bootea todos los servicios en el contenedor
     *
     * Aquí se define toda la inyección de dependencias.
     * Las factories resolverán dependencias bajo demanda.
     *
     * @return void
     */
    private function bootServices(): void
    {
        // ServicesBootstrapper está autocargado vía PSR-4 (Composer autoload)
        (new \App\Config\ServicesBootstrapper)($this->container);
    }

    /**
     * Registra todas las rutas de la aplicación
     *
     * Las rutas vienen del módulo User (u otros módulos).
     *
     * @return void
     */
    private function registerRoutes(): void
    {
        // Resolver los handlers desde el contenedor
        // Son instancias que implementan RouteHandlerInterface
        $registerUserHandler = $this->container->get('register_user_handler');
        $helloHandler = $this->container->get('hello_handler');

        // Registrar en router
        $this->router->register('POST', '/api/users/register', $registerUserHandler);
        $this->router->register('GET', '/api/users/hello', $helloHandler);
    }

    /**
     * Normaliza el path de la petición actual
     *
     * Elimina parámetros query (?x=1) y el prefijo base del proyecto.
     *
     * @return string Path normalizado
     */
    private function normalizePath(): string
    {
        // Obtener URI completa
        $uri = $_SERVER['REQUEST_URI'] ?? '/';

        // Extraer solo el path (sin query string)
        $path = parse_url($uri, PHP_URL_PATH) ?: '/';

        // Quitar prefijo base del proyecto si existe
        $basePath = '/lern_php/public';
        if (str_starts_with($path, $basePath)) {
            $path = substr($path, strlen($basePath));
            // Caso borde: si queda vacío, es root
            $path = $path === '' ? '/' : $path;
        }

        return $path;
    }

    /**
     * Envía respuesta 404 en formato JSON
     *
     * @param string $method Método HTTP de la petición
     * @param string $path Path de la petición
     * @return void
     */
    private function sendNotFound(string $method, string $path): void
    {
        http_response_code(404);
        header('Content-Type: application/json; charset=utf-8');

        echo json_encode([
            'status' => 'error',
            'msg' => 'Ruta no encontrada',
            'method' => $method,
            'path' => $path,
        ], JSON_THROW_ON_ERROR);
    }

    /**
     * Envía respuesta de error en formato JSON
     *
     * @param \Exception $exception Excepción capturada
     * @return void
     */
    private function sendError(\Exception $exception): void
    {
        http_response_code(500);
        header('Content-Type: application/json; charset=utf-8');

        $debug = isset($_ENV['APP_DEBUG']) && $_ENV['APP_DEBUG'];

        echo json_encode([
            'status' => 'error',
            'msg' => 'Error interno del servidor',
            'exception' => $debug ? $exception->getMessage() : null,
            'trace' => $debug ? $exception->getTraceAsString() : null,
        ], JSON_THROW_ON_ERROR);
    }

    /**
     * Retorna el contenedor (para acceso desde tests u otros contextos)
     *
     * @return Container
     */
    public function getContainer(): Container
    {
        return $this->container;
    }

    /**
     * Retorna el router (para acceso desde tests u otros contextos)
     *
     * @return Router
     */
    public function getRouter(): Router
    {
        return $this->router;
    }
}
