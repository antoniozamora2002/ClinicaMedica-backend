<?php

namespace App\Models;

use CodeIgniter\Model;

class EspecialidadesModel extends Model
{
    
    protected $table      = 'cat_especialidad';
    protected $primaryKey = 'esp_id';

    protected $allowedFields = [
        'esp_nombre',
        'esp_estado'
    ];

    // Obtener todas las especialidades
    public function getEspecialidades()
    {
        return $this->findAll();
    }

    // Obtener especialidad por ID
    public function getEspecialidadById($id)
    {
        return $this->where('esp_id', $id)->first();
    }
}
