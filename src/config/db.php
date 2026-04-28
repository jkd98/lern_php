<?php

date_default_timezone_set('America/Mexico_City');

function conectar(): mysqli
{
    $host = 'localhost';
    $user = 'root';
    $password = 'linux123';
    $database = 'db_self';

    $mysqli = new mysqli($host, $user, $password, $database);
    if ($mysqli->connect_errno) {
        die("Fallo al conectar a MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error);
    }

    // Forzar charset utf8mb4 (compatible con MySQL 5.7+)
    $mysqli->set_charset("utf8mb4");

    // Configurar zona horaria de MySQL
    $mysqli->query("SET time_zone = '-06:00'");

    return $mysqli;
}
