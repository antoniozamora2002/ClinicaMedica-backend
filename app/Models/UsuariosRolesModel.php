<?php

namespace App\Models;

use CodeIgniter\Model;

class UsuariosRolesModel extends Model
{
    protected $table = 'usuarios_roles';
    protected $primaryKey = 'ur_id';
    protected $DBGroup = 'seguridad';

    protected $allowedFields = [
        'usu_id',
        'rol_id',
        'ur_estado'
    ];
}
