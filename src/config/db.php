<?php

date_default_timezone_set('America/Mexico_City');

function conectar(): PDO {
    $host = 'localhost';
    $user = 'root';
    $password = 'linux123';
    $database = 'db_self';

    $dsn = "mysql:host=$host;dbname=$database;charset=utf8mb4";

    try {
        $pdo = new PDO($dsn, $user, $password, [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION, // hace que PDO lance excepciones ante cualquier error SQL, en lugar de fallar silenciosamente. 
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC, // le dice a PDO que cuando hagas fetch() devuelva arrays asociativos (como tu código MySQLi espera).
        ]);

        // Forzar charset utf8mb4 (ya lo hiciste en el DSN, pero por compatibilidad con ciertos sistemas
        // puedes ejecutar el query también; no está de más)
        $pdo->exec("SET NAMES 'utf8mb4'");

        // Configurar zona horaria de MySQL
        $pdo->exec("SET time_zone = '-06:00'");

        return $pdo;

    } catch (\PDOException $e) {
        // En tu arquitectura limpia, probablemente querrás relanzar o manejar la excepción
        // en lugar de detener el script. Pero para mantener el mismo efecto que tu versión original,
        // puedes usar die(), aunque lo ideal es loguear y lanzar una excepción propia.
        die("Fallo al conectar a MySQL: (" . $e->getCode() . ") " . $e->getMessage());
    }
}