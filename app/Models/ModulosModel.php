<?php

namespace App\Models;

use CodeIgniter\Model;

class ModulosModel extends Model
{
    // 🔥 IMPORTANTE: esta tabla está en clinica_seguridad
    protected $DBGroup = 'seguridad';

    protected $table      = 'modulos';
    protected $primaryKey = 'mo_id';

    protected $allowedFields = [
        'mo_nombre',
        'mo_descripcion',
        'mo_estado'
    ];

    // Obtener módulos activos
    public function getActivos()
    {
        return $this->where('mo_estado', 'ACTIVO')->findAll();
    }

    // Obtener módulo por ID
    public function getById($id)
    {
        return $this->where('mo_id', $id)->first();
    }
}
