<?php

declare(strict_types=1);

/**
 * Entry Point de la Aplicación
 *
 * Este archivo es el punto de entrada único para todas las peticiones HTTP.
 * Su única responsabilidad es:
 * 1. Autocargar clases (PSR-4 via Composer)
 * 2. Instanciar la Application
 * 3. Ejecutar la aplicación
 *
 * Toda la lógica de routing, DI, manejo de errores vive en Application.
 * Esto mantiene el index.php limpio y enfocado.
 */

require_once __DIR__ . '/../vendor/autoload.php';

use App\Infraestructure\Application;

// Crear e instanciar la aplicación
$app = new Application();

// Ejecutar (procesar petición HTTP actual)
$app->run();
