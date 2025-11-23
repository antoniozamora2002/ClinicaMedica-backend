<?php

namespace App\Models;

use CodeIgniter\Model;

class RolesModulosPermisosModel extends Model
{
    protected $DBGroup = 'seguridad'; // 🔥 Esta línea obliga a usar clinica_seguridad

    protected $table      = 'roles_modulos_permisos';
    protected $primaryKey = 'rmp_id';

    protected $allowedFields = [
        'rol_id',
        'mo_id',
        'per_id',
        'rmp_estado'
    ];
}
