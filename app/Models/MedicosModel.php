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
        'usu_id'
    ];

    // ============================================================
    // LISTAR MÉDICOS (SIN DUPLICADOS)
    // ============================================================
    public function getMedicos()
    {
        return $this->db->table('medicos m')
            ->select("
                m.med_id,
                m.med_profesion,
                m.med_colegiatura,
                m.med_habilitacion,
                m.med_cargo,
                m.med_otros_estudios,
                m.med_estado,

                p.per_nombres,
                p.per_apellido_paterno,
                p.per_apellido_materno,
                p.per_numero_documento,

                GROUP_CONCAT(e.esp_nombre SEPARATOR ', ') AS especialidades
            ")
            ->join('personas p', 'p.per_id = m.med_id')
            ->join('medicos_especialidades me', 'me.med_id = m.med_id', 'left')
            ->join('cat_especialidad e', 'e.esp_id = me.esp_id', 'left')
            ->groupBy('m.med_id')        // 🔥 evita duplicados por cada especialidad
            ->orderBy('m.med_id', 'DESC')
            ->get()
            ->getResultArray();
    }

    // ============================================================
    // OBTENER MÉDICO POR ID (con especialidades agrupadas)
    // ============================================================
    public function getMedicoById($id)
    {
        return $this->db->table('medicos m')
            ->select("
                m.*,
                p.*,
                GROUP_CONCAT(e.esp_nombre SEPARATOR ', ') AS especialidades
            ")
            ->join('personas p', 'p.per_id = m.med_id')
            ->join('medicos_especialidades me', 'me.med_id = m.med_id', 'left')
            ->join('cat_especialidad e', 'e.esp_id = me.esp_id', 'left')
            ->where('m.med_id', $id)
            ->groupBy('m.med_id')
            ->get()
            ->getRowArray();
    }

    // ============================================================
    // BUSCAR POR DNI
    // ============================================================
    public function buscarPorDni($dni)
    {
        return $this->db->table('personas p')
            ->select("
                p.*,
                m.*,
                GROUP_CONCAT(e.esp_nombre SEPARATOR ', ') AS especialidades
            ")
            ->join('medicos m', 'm.med_id = p.per_id')
            ->join('medicos_especialidades me', 'me.med_id = m.med_id', 'left')
            ->join('cat_especialidad e', 'e.esp_id = me.esp_id', 'left')
            ->where('p.per_numero_documento', $dni)
            ->where('m.med_estado', 'ACTIVO')
            ->groupBy('p.per_id')
            ->get()
            ->getRowArray();
    }

    // ============================================================
    // BUSCAR POR APELLIDOS
    // ============================================================
    public function buscarPorApellidos($apellido)
    {
        return $this->db->table('personas p')
            ->select("
                p.*,
                m.*,
                GROUP_CONCAT(e.esp_nombre SEPARATOR ', ') AS especialidades
            ")
            ->join('medicos m', 'm.med_id = p.per_id')
            ->join('medicos_especialidades me', 'me.med_id = m.med_id', 'left')
            ->join('cat_especialidad e', 'e.esp_id = me.esp_id', 'left')
            ->groupStart()
                ->like('p.per_apellido_paterno', $apellido)
                ->orLike('p.per_apellido_materno', $apellido)
            ->groupEnd()
            ->where('m.med_estado', 'ACTIVO')
            ->groupBy('p.per_id')
            ->get()
            ->getResultArray();
    }
}
