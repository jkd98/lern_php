<?php

declare(strict_types=1);

namespace App\Infraestructure\Container;

use InvalidArgumentException;

/**
 * Contenedor de inyección de dependencias minimalista
 *
 * Centraliza la creación y almacenamiento de servicios.
 * Implementa patrón Service Locator + Factory pattern.
 *
 * Responsabilidades:
 * - Registrar "recetas" (factories) de construcción de servicios
 * - Resolver dependencias bajo demanda
 * - Cachear instancias (singleton por request)
 */
class Container
{
    /**
     * Almacena instancias ya construidas (cache por request)
     */
    private array $instances = [];

    /**
     * Almacena "recetas" de construcción (callables que crean servicios)
     */
    private array $factories = [];

    /**
     * Registra una factory para un servicio
     *
     * @param string $id Identificador único del servicio
     * @param callable $factory Closure que retorna la instancia del servicio
     * @return void
     */
    public function register(string $id, callable $factory): void
    {
        $this->factories[$id] = $factory;
    }

    /**
     * Resuelve un servicio por su ID
     *
     * Busca en cache primero, si no existe llama la factory y cachea el resultado.
     *
     * @param string $id Identificador del servicio
     * @return mixed La instancia del servicio
     * @throws InvalidArgumentException Si el servicio no está registrado
     */
    public function get(string $id): mixed
    {
        // 1) Si ya existe en cache, reutiliza la instancia (singleton)
        if (array_key_exists($id, $this->instances)) {
            return $this->instances[$id];
        }

        // 2) Si no hay receta, error claro
        if (!array_key_exists($id, $this->factories)) {
            throw new InvalidArgumentException("Service '{$id}' is not defined.");
        }

        // 3) Construye, cachea y retorna
        // Pasa $this para que las factories puedan resolver dependencias
        $this->instances[$id] = ($this->factories[$id])($this);

        return $this->instances[$id];
    }

    /**
     * Verifica si un servicio está registrado
     *
     * @param string $id Identificador del servicio
     * @return bool True si está registrado, false en caso contrario
     */
    public function has(string $id): bool
    {
        return array_key_exists($id, $this->factories);
    }

    /**
     * Limpia todas las instancias en cache
     *
     * Útil para tests o resetear estado entre requests en algunos contextos.
     *
     * @return void
     */
    public function clearInstances(): void
    {
        $this->instances = [];
    }
}
