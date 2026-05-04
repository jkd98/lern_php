<?php
declare(strict_types=1);

namespace App\User\Http\Controllers;

use App\User\Application\Services\RegisterUser\RegisterUserUseCase;
use App\User\Application\Exceptions\EmailAlreadyRegisteredException;

use App\User\Application\Services\RegisterUser\RegisterUserRequestDTO;

final class RegisterUserController {
    
    public function __construct(private RegisterUserUseCase $service){}
    
    /**
     * Recibe una petición POST para registrar a un usuario
     */
    public function execute(array $requestData): string {
        try {
            // Crear el request DTO
            $data = new RegisterUserRequestDTO(
                name:     $requestData['name']  ?? '',
                email:    $requestData['email'] ?? '',
                password: $requestData['password'] ?? ''
            );

            // Ejecutar el servicio 
            $responseDTO = $this->service->execute($data);

            // Enviar la respuesta
            http_response_code(201);
            return json_encode([
                'status' => 'success',
                'msg'   => 'Usuario registrado correctamente',
                'data'  => [
                    'id'    => $responseDTO->id,
                    'name'  => $responseDTO->name,
                    'email' => $responseDTO->email,
                ]
            ], JSON_THROW_ON_ERROR);

        } catch (EmailAlreadyRegisteredException $e) {
            http_response_code(409);
            return json_encode([
                'status'  => 'error',
                'msg'    => $e->getMessage(),
            ], JSON_THROW_ON_ERROR);
        } catch (\InvalidArgumentException $e) {
            http_response_code(422);
            return json_encode([
                'status'  => 'error',
                'msg'    => $e->getMessage(),
            ], JSON_THROW_ON_ERROR);
        } catch (\Throwable $e) {
            http_response_code(500);
            return json_encode([
                'status'  => 'error',
                'msg'    => 'Error interno del servidor.',
            ], JSON_THROW_ON_ERROR);
        }
    }
}