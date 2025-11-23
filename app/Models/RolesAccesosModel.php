<?php

namespace App\Models;

use CodeIgniter\Model;

class RolesAccesosModel extends Model
{
    protected $DBGroup = 'seguridad'; // 🔥 IMPORTANTE

    protected $table      = 'roles_accesos';
    protected $primaryKey = 'ra_id';

    protected $allowedFields = [
        'rol_id',
        'mo_id',
        'ra_estado'
    ];
}
