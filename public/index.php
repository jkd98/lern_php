<?php
declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../src/config/bootstrap.php';

// 1) Crear contenedor de dependencias
$container = appContainer();
$get = $container['get'];

// 2) Cargar factory de rutas del módulo User
$routesFactory = require __DIR__ . '/../src/User/Http/Routes/routes.php';

// 3) Resolver dependencias para rutas (controladores ya construidos)
$routes = $routesFactory(
    $get('register_user_controller')
);

// 4) Leer método y path real de la petición
//
// $_SERVER['REQUEST_METHOD'] trae el verbo HTTP que llegó a este front controller.
// Ejemplos comunes: GET, POST, PUT, PATCH, DELETE.
// Usamos ?? 'GET' como valor de respaldo por seguridad.
$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';

// $_SERVER['REQUEST_URI'] trae la URI completa que pidió el cliente,
// incluyendo path y, si existe, query string.
// Ejemplo real:
//   /lern_php/public/api/users/register?debug=1
// Usamos '/' como respaldo si no existe.
$uri = $_SERVER['REQUEST_URI'] ?? '/';

// parse_url(..., PHP_URL_PATH) extrae SOLO el path, sin parámetros (?x=1).
// Del ejemplo anterior retorna:
//   /lern_php/public/api/users/register
//
// ¿Por qué hacemos esto?
// Porque las rutas del router se comparan contra paths "limpios".
// Si dejas el query string, el match fallaría aunque la ruta exista.
$path = parse_url($uri, PHP_URL_PATH) ?: '/';

// 5) Quitar prefijo base del proyecto si existe (/lern_php/public)
//
// Tus rutas internas están definidas como:
//   /api/users/register
// pero el servidor te entrega el path completo del proyecto:
//   /lern_php/public/api/users/register
//
// Si comparáramos directo ese path "largo" con el routePath "corto",
// nunca habría coincidencia.
// Por eso normalizamos quitando el prefijo base.
$basePath = '/lern_php/public';

// str_starts_with valida si el path recibido comienza con ese prefijo.
// Si estás en otro entorno donde la app no cuelga de /lern_php/public,
// esta condición puede no cumplirse y se deja el path como llegó.
if (str_starts_with($path, $basePath)) {
    // substr recorta la parte inicial del path para dejar la ruta relativa.
    // Ejemplo:
    //   /lern_php/public/api/users/register -> /api/users/register
    $path = substr($path, strlen($basePath));

    // Caso borde:
    // Si la URL fue exactamente /lern_php/public, después del recorte queda
    // cadena vacía ''. En routers, el equivalente correcto es '/'.
    $path = $path === '' ? '/' : $path;
}

// 6) Match simple exacto: [METHOD, PATH, HANDLER]
foreach ($routes as [$routeMethod, $routePath, $handler]) {
    if ($method === $routeMethod && $path === $routePath) {
        $response = $handler();
        if (is_string($response)) {
            echo $response;
        }
        exit;
    }
}

// 7) Si no hay match, 404 JSON
http_response_code(404);
header('Content-Type: application/json; charset=utf-8');
echo json_encode([
    'status' => 'error',
    'msg' => 'Ruta no encontrada',
    'method' => $method,
    'path' => $path,
], JSON_THROW_ON_ERROR);
