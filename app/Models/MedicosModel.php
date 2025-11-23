<?php

namespace App\Models;

use CodeIgniter\Model;

class MedicosModel extends Model
{
    protected $table      = 'medicos';
    protected $primaryKey = 'med_id';
    protected $useAutoIncrement = false;

    protected $allowedFields = [
        'med_id',
        'med_profesion',
        'med_colegiatura',
        'med_habilitacion',
        'med_cargo',
        'med_otros_estudios',
        'med_estado',
        'usu_id'   // Si decides asociar el médico a un usuario
    ];

    // Obtener médico con detalles de especialidades
    public function getMedicos()
    {
        return $this->select('
            medicos.med_id,
            medicos.med_profesion,
            medicos.med_colegiatura,
            medicos.med_habilitacion,
            medicos.med_cargo,
            medicos.med_otros_estudios,
            medicos.med_estado,
            personas.per_nombres,
            personas.per_apellido_paterno,
            personas.per_apellido_materno,
            personas.per_numero_documento
        ')
        ->join('personas', 'medicos.med_id = personas.per_id')
        ->join('medicos_especialidades', 'medicos.med_id = medicos_especialidades.med_id', 'left')
        ->join('cat_especialidad', 'medicos_especialidades.esp_id = cat_especialidad.esp_id', 'left')
        ->findAll();
    }

    // Obtener médico por ID
    public function getMedicoById($id)
    {
        return $this->select('
            medicos.*,
            personas.*
        ')
        ->join('personas', 'medicos.med_id = personas.per_id')
        ->where('medicos.med_id', $id)
        ->first();
    }
}
