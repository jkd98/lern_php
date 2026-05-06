<?php

declare(strict_types=1);

namespace App\Config;

use PDO;
use PDOException;

/**
 * DatabaseConnection: Factory para conexión PDO a MySQL
 *
 * Responsabilidad:
 * - Crear y configurar la conexión a base de datos
 * - Manejar credenciales
 * - Configurar charset y timezone
 * - Lanzar excepciones en caso de error
 *
 * Patrón: Factory Method
 */
class DatabaseConnection
{
    /**
     * Crea una conexión PDO configurada a MySQL
     *
     * Configuración:
     * - Charset: utf8mb4 (soporta emojis y caracteres especiales)
     * - Timezone: -06:00 (México)
     * - Modo de error: ERRMODE_EXCEPTION (lanza excepciones)
     * - Fetch mode: FETCH_ASSOC (arrays asociativos)
     *
     * @return PDO Instancia de conexión a base de datos
     * @throws PDOException Si la conexión falla
     */
    public static function create(): PDO
    {
        // Configuración de conexión
        $host = 'localhost';
        $user = 'root';
        $password = 'linux123';
        $database = 'db_self';

        // DSN (Data Source Name) - string de conexión a MySQL
        $dsn = "mysql:host=$host;dbname=$database;charset=utf8mb4";

        try {
            // Crear instancia PDO con configuración
            $pdo = new PDO($dsn, $user, $password, [
                // Modo de error: lanza excepciones en lugar de fallar silenciosamente
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,

                // Modo de fetch por defecto: arrays asociativos
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            ]);

            // Configurar charset a nivel de sesión (redundante pero por compatibilidad)
            $pdo->exec("SET NAMES 'utf8mb4'");

            // Configurar zona horaria de MySQL a México (-06:00)
            $pdo->exec("SET time_zone = '-06:00'");

            // Establecer zona horaria de PHP
            date_default_timezone_set('America/Mexico_City');

            return $pdo;

        } catch (PDOException $e) {
            // Relanzar la excepción para que sea manejada por la aplicación
            // En producción, deberías loguear el error y no exponer detalles sensibles
            throw new PDOException(
                "Error de conexión a base de datos: " . $e->getMessage(),
                (int)$e->getCode()
            );
        }
    }
}
