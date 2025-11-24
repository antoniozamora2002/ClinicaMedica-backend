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

    public function buscarPorDni($dni)
    {
        return $this->db->table('personas p')
            ->select('p.*, m.*, GROUP_CONCAT(e.esp_nombre) AS especialidades')
            ->join('medicos m', 'm.med_id = p.per_id')
            ->join('medicos_especialidades me', 'me.med_id = m.med_id', 'left')
            ->join('cat_especialidad e', 'e.esp_id = me.esp_id', 'left') // 🔥 CORREGIDO
            ->where('p.per_numero_documento', $dni)
            ->where('m.med_estado', 'ACTIVO')
            ->groupBy('p.per_id')
            ->get()
            ->getRowArray();
    }

    
    public function buscarPorApellidos($apellido)
    {
        return $this->db->table('personas p')
            ->select('p.*, m.*, GROUP_CONCAT(e.esp_nombre) AS especialidades')
            ->join('medicos m', 'm.med_id = p.per_id')
            ->join('medicos_especialidades me', 'me.med_id = m.med_id', 'left')
            ->join('cat_especialidad e', 'e.esp_id = me.esp_id', 'left') // 🔥 CORREGIDO
            ->like('p.per_apellido_paterno', $apellido)
            ->orLike('p.per_apellido_materno', $apellido)
            ->where('m.med_estado', 'ACTIVO')
            ->groupBy('p.per_id')
            ->get()
            ->getResultArray();
    }

    

}
