# lern_php

## Objetivo arquitectónico
Este proyecto busca implementar **DDD (Domain-Driven Design)** sobre una arquitectura **N-tier (por capas)** para el módulo de usuarios.

La separación deseada es:
- `Presentation Layer`
- `Application Layer`
- `Domain Layer`
- `Infrastructure Layer`

## Capas (DDD + N-tier)

## 1. Presentation Layer
Responsabilidad:
- Recibir requests HTTP.
- Transformar entrada/salida (JSON <-> DTO).
- Traducir excepciones de negocio a códigos HTTP.

Elementos actuales:
- `src/User/Http/Routes/routes.php`
- `src/User/Http/Controllers/RegisterUserController.php`

## 2. Application Layer
Responsabilidad:
- Orquestar casos de uso.
- Coordinar reglas de dominio y repositorios.
- No conocer detalles de framework ni SQL.

Elementos actuales:
- `src/User/Application/Services/RegisterUser/RegisterUserUseCase.php`
- `src/User/Application/Services/RegisterUser/RegisterUserService.php`
- `src/User/Application/Services/RegisterUser/RegisterUserRequestDTO.php`
- `src/User/Application/Services/RegisterUser/RegisterUserResponseDTO.php`
- `src/User/Application/Exceptions/EmailAlreadyRegisteredException.php`

## 3. Domain Layer
Responsabilidad:
- Contener el modelo de negocio puro.
- Definir entidades, value objects, invariantes y contratos.

Elementos actuales:
- Entidad: `src/User/Domain/Entities/User.php`
- Contrato de persistencia: `src/User/Domain/IUserRepository.php`
- Value Objects:
  - `src/User/Domain/ValueObjects/UserName.php`
  - `src/User/Domain/ValueObjects/Email.php`
  - `src/User/Domain/ValueObjects/Password.php`
  - `src/Shared/Domain/ValueObjects/UUIDv7.php`
- Excepciones de dominio:
  - `src/User/Domain/Exceptions/InvalidEmailFormatException.php`

## 4. Infrastructure Layer
Responsabilidad:
- Implementar adaptadores técnicos (DB, servicios externos, correo, etc.).
- Implementar interfaces definidas por Domain.

Elementos actuales:
- Repositorio PDO:
  - `src/User/Infraestructure/Persistence/Pdo/PdoUserRepository.php`
- Servicios externos:
  - `src/User/Infraestructure/ExternalServices/MailService.php`
- Configuración técnica:
  - `src/config/db.php`
  - `src/config/bootstrap.php`

## Regla de dependencias (muy importante)
En DDD + N-tier, la dependencia debe apuntar hacia adentro:

`Presentation -> Application -> Domain`
`Infrastructure -> Domain`

Reglas:
- Domain no depende de Application, Presentation ni Infrastructure.
- Application depende de contratos del Domain, no de implementaciones técnicas.
- Infrastructure implementa contratos del Domain (ej. repositorios).
- Presentation invoca casos de uso de Application.

## Flujo del caso de uso: Registrar Usuario

```txt
HTTP POST /api/users/register
   -> Route
   -> RegisterUserController (Presentation)
   -> RegisterUserService::execute (Application)
   -> User, Email, Password, UserName, UUIDv7 (Domain)
   -> IUserRepository (Domain contract)
   -> PdoUserRepository (Infrastructure impl)
   -> ResponseDTO
   -> JSON + HTTP status
```

## Mapeo DDD del código actual
- `Entity`: `User`
- `Value Objects`: `Email`, `Password`, `UserName`, `UUIDv7`
- `Repository Contract`: `IUserRepository`
- `Use Case`: `RegisterUserUseCase` + `RegisterUserService`
- `DTOs`: `RegisterUserRequestDTO`, `RegisterUserResponseDTO`
- `Controller`: `RegisterUserController`
- `Infrastructure Adapter`: `PdoUserRepository`

## Estado actual (gap contra el objetivo)
La estructura sí refleja la intención DDD + N-tier, pero el vertical slice de registro todavía no está completo.

Brechas principales detectadas:
1. `UserName` tiene typo en constructor (`__contruct`), rompe la instanciación.
2. `RegisterUserRequestDTO` usa propiedades privadas sin getters y el servicio intenta acceso directo.
3. `RegisterUserResponseDTO` no define constructor pero se instancia con named args.
4. `EmailAlreadyRegisteredException` está sin namespace consistente.
5. `PdoUserRepository` está incompleto y con namespace inconsistente (`Eloquent` vs `Pdo`).
6. `db.php` tiene credenciales hardcodeadas (debe migrar a variables de entorno).

## Estructura actual del proyecto

```txt
src/
  config/
    bootstrap.php
    db.php
  Shared/
    Domain/ValueObjects/UUIDv7.php
  User/
    Http/
      Routes/routes.php
      Controllers/RegisterUserController.php
    Application/
      Services/RegisterUser/
      Services/UpdateUser/
      Exceptions/
    Domain/
      Entities/
      ValueObjects/
      Exceptions/
      IUserRepository.php
    Infraestructure/
      Persistence/Pdo/
      ExternalServices/
```

## Recomendaciones para consolidar DDD + N-tier
1. Corregir errores de contrato/namespace y completar repositorio PDO.
2. Estandarizar DTOs (constructores + getters/readonly públicos según convención).
3. Definir esquema SQL y migraciones para `users`.
4. Externalizar configuración (`.env`) y evitar secretos en código.
5. Agregar tests:
- Unitarios: Value Objects y Use Case.
- Integración: endpoint `POST /api/users/register`.
6. Renombrar `Infraestructure` a `Infrastructure` para consistencia.

## Conclusión
El proyecto está bien orientado a **DDD + arquitectura por capas N-tier**. La base conceptual está correcta; el siguiente paso es cerrar las brechas de implementación para que la separación por capas no solo exista en carpetas, sino también en ejecución real y mantenible.
