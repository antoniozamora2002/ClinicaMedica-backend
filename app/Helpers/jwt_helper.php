<?php

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

/**
 * Genera un JWT con payload personalizado
 */
function generateJWT(array $payload)
{
    $key = getenv('JWT_SECRET');

    if (!isset($payload['iat'])) {
        $payload['iat'] = time();
    }

    if (!isset($payload['exp'])) {
        $payload['exp'] = time() + 3600; // 1 hora
    }

    return JWT::encode($payload, $key, 'HS256');
}

/**
 * Verifica un JWT y retorna el contenido decodificado como array
 */
function verifyJWT(string $token): ?array
{
    try {
        $key = getenv('JWT_SECRET');
        $decoded = JWT::decode($token, new Key($key, 'HS256'));

        // Convertir stdClass → array
        return json_decode(json_encode($decoded), true);
    } catch (\Exception $e) {
        return null;
    }
}
