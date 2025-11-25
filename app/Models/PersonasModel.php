<?php

namespace App\Models;

use CodeIgniter\Model;

class PersonasModel extends Model
{
    protected $table      = 'personas';
    protected $primaryKey = 'per_id';

    protected $allowedFields = [
        'per_tipo_documento_id',
        'per_numero_documento',
        'per_nombres',
        'per_apellido_paterno',
        'per_apellido_materno',
        'per_fecha_nacimiento',
        'per_sexo',
        'per_telefono',
        'per_correo',
        'per_estado_civil',
        'per_nacionalidad',
        'per_estado'
    ];

    
}
