<?php

namespace App\Models;

use CodeIgniter\Model;

class PermisosModel extends Model
{
    // BASE DE DATOS CORRECTA 🔥
    protected $DBGroup = 'seguridad';

    protected $table      = 'permisos';
    protected $primaryKey = 'permi_id';

    protected $allowedFields = [
        'permi_nombre',
        'permi_descripcion',
        'permi_estado'
    ];

    // Obtener permisos activos
    public function getActivos()
    {
        return $this->where('permi_estado', 'ACTIVO')->findAll();
    }

    // Obtener permiso por ID
    public function getById($id)
    {
        return $this->where('permi_id', $id)->first();
    }
}
