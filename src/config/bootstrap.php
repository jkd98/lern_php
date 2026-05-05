<?php
declare(strict_types=1);

require_once __DIR__ . '/container.php';

/*
|--------------------------------------------------------------------------
| Bootstrap de la aplicación
|--------------------------------------------------------------------------
| "Bootstrap" es el punto de arranque técnico.
| Aquí NO va lógica de negocio.
| Aquí solo inicializamos infraestructura de app (contenedor, config, etc.).
|
| En este proyecto, bootstrap expone el contenedor para que el front-controller
| (por ejemplo index.php o tu router principal) pueda pedir dependencias.
*/
function appContainer(): array
{
    /*
    | buildContainer() vive en container.php y retorna una estructura mínima:
    | [
    |   'get' => function(string $id) { ... }
    | ]
    |
    | Es decir, devolvemos un "resolver" de servicios por id.
    */
    return buildContainer();
}
