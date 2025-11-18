<?php

function userCan($request, string $module, string $requiredPermission): bool
{
    if (!isset($request->userData)) {
        return false;
    }

    $user = $request->userData; // objeto stdClass

    // Verifica que exista el módulo
    if (!isset($user->permisos->{$module})) {
        return false;
    }

    // Lista de permisos del módulo
    $permissions = $user->permisos->{$module};

    return in_array($requiredPermission, $permissions);
}

function userHasModule($request, string $module): bool
{
    if (!isset($request->userData)) {
        return false;
    }

    $user = $request->userData;

    return isset($user->permisos->{$module});
}
