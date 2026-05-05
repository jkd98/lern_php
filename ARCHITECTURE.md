# 🏗️ Refactorización a Arquitectura Basada en Clases

## Resumen de Cambios

Se ha transformado la aplicación de un modelo **function-based** a un modelo **class-based** con arquitectura profesional N-capas + DDD.

---

## 📂 Nueva Estructura

```
src/
├── Config/
│   └── ServicesBootstrapper.php      ← Registra todos los servicios en el contenedor
├── Infraestructure/
│   ├── Application.php               ← Orquestador principal de la app
│   ├── Container/
│   │   └── Container.php             ← Inyección de dependencias
│   └── Router/
│       └── Router.php                ← Enrutador minimalista
├── User/
│   ├── Application/
│   ├── Domain/
│   ├── Http/
│   │   ├── Controllers/
│   │   └── Routes/
│   │       ├── RouteHandlerInterface.php        ← Contrato para handlers
│   │       ├── RegisterUserHandler.php          ← Handler POST /api/users/register
│   │       └── HelloHandler.php                 ← Handler GET /api/users/hello
│   └── Infraestructure/
└── Shared/
    └── Domain/

public/
└── index.php                         ← Entry point limpio (solo 3 líneas!)
```

---

## 🔄 Flujo de Ejecución

```
1. index.php (entry point)
        ↓
2. new Application()
        ↓
3. Application::run()
        ├─ bootServices()
        │   └─ ServicesBootstrapper registra toda la DI
        ├─ registerRoutes()
        │   ├─ Resuelve handlers del contenedor
        │   └─ Registra en router
        ├─ normalizePath()
        │   └─ Extrae path limpio de $_SERVER
        ├─ router->match(method, path)
        │   └─ Retorna handler si coincide
        └─ handler() // Invoca __invoke()
```

---

## 🎯 Componentes Principales

### 1. **Application.php** (Orquestador)
- Centro de control de la aplicación
- Coordina Container + Router
- Procesa peticiones HTTP
- Maneja errores y respuestas

**Responsabilidades:**
- Boot de servicios
- Boot de rutas
- Normalización de paths
- Ejecución de handlers
- Manejo de errores (404, 500)

### 2. **Container.php** (Inyección de Dependencias)
```php
$container = new Container();
$container->register('pdo', function($c) { return conectar(); });
$container->register('user_service', function($c) { 
    return new UserService($c->get('pdo'));
});

$pdo = $container->get('pdo');  // Resuelve dependencias
```

**Características:**
- Registro de servicios con factories
- Resolución lazy (bajo demanda)
- Caché de instancias (singleton por request)
- Resolución de dependencias automática

### 3. **Router.php** (Enrutador)
```php
$router = new Router();
$router->register('POST', '/api/users', $handler);
$router->register('GET', '/api/users/{id}', $handler);

$handler = $router->match('POST', '/api/users');  // Retorna handler o null
```

**Características:**
- Match exacto de rutas
- Soporta todos los verbos HTTP
- Type-hint con `RouteHandlerInterface`

### 4. **RouteHandlerInterface** (Contrato)
```php
interface RouteHandlerInterface {
    public function __invoke(): string;
}

class MyHandler implements RouteHandlerInterface {
    public function __invoke(): string {
        // Procesa request, delega a Application Layer
        return json_encode(['status' => 'ok']);
    }
}
```

**Beneficios:**
- Contrato explícito para handlers
- Type-hinting claro
- Facilita testing/mocking

### 5. **ServicesBootstrapper.php** (Configuración)
Define todas las factories de servicios:
```php
$container->register('register_user_handler', function($c) {
    return new RegisterUserHandler($c->get('register_user_controller'));
});
```

---

## 🏛️ Arquitectura N-Capas

```
┌─────────────────────────────────────┐
│     Presentation Layer (HTTP)       │
│  - RouteHandler (HTTP I/O)          │
│  - Controllers (Orquestación)       │
└─────────────────────────────────────┘
            ↓
┌─────────────────────────────────────┐
│   Application Layer (Casos de Uso)  │
│  - Services (Lógica de negocio)     │
│  - DTOs (Transfer Objects)          │
└─────────────────────────────────────┘
            ↓
┌─────────────────────────────────────┐
│      Domain Layer (Lógica Pura)     │
│  - Entities (User, Post, etc)       │
│  - Value Objects                    │
│  - Repository Interfaces            │
│  - Business Rules                   │
└─────────────────────────────────────┘
            ↓
┌─────────────────────────────────────┐
│  Infrastructure Layer (Técnica)     │
│  - Repository Implementations (BD)  │
│  - ORM/Query Builders               │
│  - External Services                │
│  - Container, Router, etc           │
└─────────────────────────────────────┘
```

---

## ✅ Ventajas de esta Refactorización

| Aspecto | Antes | Ahora |
|---------|-------|-------|
| **Testabilidad** | Closures anónimos difíciles de mockecar | ✅ Interfaces claras, fácil testing |
| **Mantenibilidad** | Lógica dispersa en routes.php | ✅ Responsabilidades bien separadas |
| **Escalabilidad** | Crece caótica con más rutas | ✅ Fácil agregar nuevos módulos/rutas |
| **Debugging** | Stack traces confusos | ✅ Stack traces claros con nombres de clase |
| **Reutilización** | Handlers no reutilizables | ✅ Handlers inyectables en múltiples contextos |
| **SOLID** | Viola SRP, DIP | ✅ Cumple SRP, DIP, ISP |
| **Documentación** | Implícita en comentarios | ✅ Explícita con tipos e interfaces |

---

## 📝 Ejemplo: Agregar Nueva Ruta

### Antes (Closures)
```php
// routes.php
return static function($ctrl) {
    return [
        ['POST', '/api/users', static function() use ($ctrl) { ... }],
    ];
};
```

### Ahora (Clases)
1. **Crear Handler:**
```php
// src/User/Http/Routes/CreateUserHandler.php
class CreateUserHandler implements RouteHandlerInterface {
    public function __invoke(): string { ... }
}
```

2. **Registrar en Container:**
```php
// src/Config/ServicesBootstrapper.php
$container->register('create_user_handler', function($c) {
    return new CreateUserHandler($c->get('user_service'));
});
```

3. **Registrar en Application:**
```php
// src/Infraestructure/Application.php
$this->router->register('POST', '/api/users', 
    $this->container->get('create_user_handler'));
```

---

## 🔍 Cómo Funciona el DI Container

```php
// Registrar una factory
$container->register('user_repository', function($container) {
    return new UserRepository($container->get('pdo'));
});

// Primera invocación: construye y cachea
$repo1 = $container->get('user_repository');  // ← Construye

// Segunda invocación: retorna del caché
$repo2 = $container->get('user_repository');  // ← Del caché
// $repo1 === $repo2 (misma instancia - singleton)
```

---

## 🧪 Testing

Ahora es mucho más fácil testear con la arquitectura de clases:

```php
// test.php
$handler = new RegisterUserHandler($mockController);
$response = $handler();
// Assert response...
```

Con closures era casi imposible mockecar.

---

## 🚀 Próximos Pasos

1. **Agregar más rutas:** Sigue el patrón Handler + Bootstrapper
2. **Agregar validación:** Crea un `RequestValidator` en Application Layer
3. **Agregar logging:** Inyecta `LoggerInterface` en servicios
4. **Agregar middleware:** Crea `MiddlewareStack` antes de ejecutar handlers
5. **Separar módulos:** Cada módulo (User, Post, etc) en su carpeta con su propio bootstrapper

---

## 📚 Archivos Eliminados

✅ Removidos (reemplazados por clases):
- `src/config/bootstrap.php`
- `src/config/container.php`
- `src/User/Http/Routes/routes.php`

---

## 📖 Referencias

- **DDD:** Domain-Driven Design (Eric Evans)
- **N-Capas:** https://docs.microsoft.com/en-us/dotnet/architecture/n-tier/
- **SOLID:** https://en.wikipedia.org/wiki/SOLID
- **Service Locator vs DI:** https://martinfowler.com/articles/injection.html

