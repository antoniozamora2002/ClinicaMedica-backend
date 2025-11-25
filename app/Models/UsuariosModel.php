<?php

namespace App\Models;

use CodeIgniter\Model;

class UsuariosModel extends Model
{
    protected $DBGroup = 'seguridad';

    protected $table      = 'usuarios';
    protected $primaryKey = 'usu_id';

    protected $allowedFields = [
        'usu_login',
        'usu_password_hash',
        'usu_nombre_completo',
        'usu_correo',
        'usu_estado',
        'usu_creado_en'
    ];
}
