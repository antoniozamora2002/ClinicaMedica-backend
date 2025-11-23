<?php

namespace App\Models;

use CodeIgniter\Model;

class RolesModulosPermisosModel extends Model
{
    // 🔥 Este grupo hará que CI use la base clinica_seguridad
    protected $DBGroup = 'seguridad';

    protected $table      = 'roles_modulos_permisos';
    protected $primaryKey = 'rmp_id';

    protected $allowedFields = [
        'ra_id',        // ID de roles_accesos (rol + módulo)
        'per_id',       // ID del permiso (READ, CREATE, UPDATE, DELETE)
        'rmp_estado'    // ACTIVO / INACTIVO
    ];

    // Opcional: devolver solo activos por defecto
    protected $useSoftDeletes = false;
}
