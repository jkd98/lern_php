<?php

declare(strict_types=1);

use App\User\Http\Controllers\RegisterUserController;

/*
|--------------------------------------------------------------------------
| Routes Factory (Presentation Layer)
|--------------------------------------------------------------------------
| Este archivo NO ejecuta una ruta por sí mismo.
| Lo que hace es RETORNAR una función (closure) que, cuando se invoque,
| devolverá el arreglo de rutas de este módulo.
|
| ¿Por qué retornar una función y no el array directo?
| - Porque así podemos inyectar dependencias (ej. controladores ya creados)
|   desde afuera (bootstrap/container).
| - Con eso evitamos construir objetos en la capa de rutas.
*/
return static function (RegisterUserController $registerUserController): array {
    /*
    |------------------------------------------------------------------
    | Definición de rutas
    |------------------------------------------------------------------
    | Formato esperado por tu mini-router:
    | [HTTP_METHOD, PATH, HANDLER]
    */
    return [
        [
            // 1) Método HTTP que debe coincidir con la petición entrante.
            'POST',

            // 2) Path exacto del endpoint.
            '/api/users/register',

            /*
            | 3) Handler (closure) que se ejecuta cuando método+path coinciden.
            |
            | use ($registerUserController):
            | - "captura" una variable externa para usarla dentro de la closure.
            | - Sin use(...), dentro de function() esa variable no existe.
            |
            | static function:
            | - Evita que la closure use accidentalmente $this.
            | - Es buena práctica cuando no necesitas contexto de objeto.
            */
            static function () use ($registerUserController) {
                /*
                | Leer body del request:
                | php://input trae el contenido crudo del POST (raw body).
                */
                $rawBody = file_get_contents('php://input');

                /*
                | Parsear JSON a array asociativo:
                | - true => array (no objeto stdClass)
                | - ?? [] => si viene null/invalid, usamos array vacío
                */
                $data = json_decode($rawBody, true) ?? [];

                /*
                | Delegación al controlador:
                | Aquí termina la responsabilidad de la ruta.
                | La lógica de caso de uso vive en Application/Domain.
                */
                return $registerUserController->execute($data);
            }
        ],
    ];
};
