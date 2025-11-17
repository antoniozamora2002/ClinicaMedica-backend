<?php

namespace App\Filters;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class JWTAuthFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        // Obtener el header Authorization
        $authHeader = $request->getHeaderLine('Authorization');

        // Verificar que exista el token
        if (!$authHeader || !str_starts_with($authHeader, 'Bearer ')) {
            return response()->setStatusCode(401)
                ->setJSON(['error' => 'No autorizado: falta token Bearer']);
        }

        // Obtener el token
        $token = substr($authHeader, 7);

        try {
            // Decodificar el token
            $decoded = JWT::decode($token, new Key(getenv('JWT_SECRET'), 'HS256'));

            // Guardar los datos del usuario para el siguiente paso
            $request->userData = $decoded;
        } catch (\Throwable $e) {
            return response()->setStatusCode(401)
                ->setJSON(['error' => 'Token inválido o expirado']);
        }

        return $request;
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Nada necesario después
    }
}
