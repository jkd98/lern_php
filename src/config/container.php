<?php
declare(strict_types=1);

require_once __DIR__ . '/db.php';

use App\User\Infraestructure\Factories\RegisterUserControllerFactory;

/*
|--------------------------------------------------------------------------
| DI Container Minimalista
|--------------------------------------------------------------------------
| Objetivo:
| - Centralizar la creación de dependencias (wiring).
| - Evitar "new" repartidos por rutas/controladores.
| - Respetar la dirección de dependencias de DDD/N-tier.
*/
function buildContainer(): array
{
    // Guarda instancias ya construidas (cache por request).
    $instances = [];

    // "Recetas" de construcción de servicios.
    $factories = [
        // Servicio base: conexión PDO.
        'pdo' => static function () {
            return conectar();
        },

        // Servicio compuesto: controlador de registro.
        // Depende de pdo y se construye vía Factory dedicada.
        'register_user_controller' => static function (array $c) {
            return RegisterUserControllerFactory::create($c['pdo']);
        },
    ];

    // API pública del contenedor.
    return [
        'get' => static function (string $id) use (&$instances, $factories) {
            // 1) Si ya existe, reutiliza la instancia (singleton por request).
            if (array_key_exists($id, $instances)) {
                return $instances[$id];
            }

            // 2) Si no hay receta, error claro.
            if (!array_key_exists($id, $factories)) {
                throw new InvalidArgumentException("Service '{$id}' is not defined.");
            }

            // 3) Construye, guarda y retorna.
            $instances[$id] = $factories[$id]($instances);
            return $instances[$id];
        },
    ];
}
