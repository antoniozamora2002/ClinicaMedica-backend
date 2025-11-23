<?php

namespace App\Models;

use CodeIgniter\Model;

class ClasificacionTriajeModel extends Model
{
    protected $table      = 'cat_clasificacion_triaje';
    protected $primaryKey = 'cla_id';

    protected $allowedFields = [
        'cla_nombre',
        'cla_color',
        'cla_prioridad',
        'cla_estado'
    ];

    // Obtener todas las clasificaciones
    public function getClasificaciones()
    {
        return $this->findAll();
    }

    // Obtener clasificación por ID
    public function getClasificacionById($id)
    {
        return $this->where('cla_id', $id)->first();
    }
}
