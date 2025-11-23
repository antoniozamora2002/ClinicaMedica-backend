<?php

namespace App\Models;

use CodeIgniter\Model;

class RolesModel extends Model
{
    protected $DBGroup = 'seguridad'; // 🔥 IMPORTANTE

    protected $table      = 'roles';
    protected $primaryKey = 'rol_id';

    protected $allowedFields = [
        'rol_nombre',
        'rol_descripcion',
        'rol_estado'
    ];
}
