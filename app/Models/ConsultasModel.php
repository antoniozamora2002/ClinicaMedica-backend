<?php

namespace App\Models;

use CodeIgniter\Model;

class ConsultasModel extends Model
{
    protected $table      = 'consultas';
    protected $primaryKey = 'con_id';

    protected $allowedFields = [
        'his_id',
        'med_id',
        'tip_id',
        'ase_id',
        'con_fecha_consulta',
        'con_tipo_servicio',
        'con_area',
        'con_num_poliza',
        'con_razon_social',
        'con_ocupacion',
        'con_turno',
        'con_horas_extra',
        'con_enf_cronicas',
        'con_alergias',
        'con_medicamentos_actuales',
        'con_antecedentes_quirurgicos',
        'con_habitos_nocivos',
        'con_habitos_frecuencia',
        'con_sintomas',
        'con_tiempo_enfermedad',
        'con_motivo_consulta',
        'con_diagnostico',
        'con_tratamiento',
        'con_receta',
        'con_observaciones',
        'con_cie10_principal'
    ];

    // Obtener consultas por paciente
    public function getConsultasByPaciente($pacId)
    {
        return $this->select('
            consultas.con_id,
            consultas.con_fecha_consulta,
            consultas.con_tipo_servicio,
            consultas.con_area,
            consultas.con_num_poliza,
            consultas.con_razon_social,
            consultas.con_ocupacion,
            consultas.con_turno,
            consultas.con_horas_extra,
            consultas.con_enf_cronicas,
            consultas.con_alergias,
            consultas.con_medicamentos_actuales,
            consultas.con_antecedentes_quirurgicos,
            consultas.con_habitos_nocivos,
            consultas.con_habitos_frecuencia,
            consultas.con_sintomas,
            consultas.con_tiempo_enfermedad,
            consultas.con_motivo_consulta,
            consultas.con_diagnostico,
            consultas.con_tratamiento,
            consultas.con_receta,
            consultas.con_observaciones,
            consultas.con_cie10_principal,
            medicos.med_id,
            medicos.med_profesion,
            personas.per_nombres,
            personas.per_apellido_paterno,
            personas.per_apellido_materno
        ')
        ->join('historias_clinicas', 'historias_clinicas.his_id = consultas.his_id')
        ->join('pacientes', 'pacientes.pac_id = historias_clinicas.pac_id')
        ->join('medicos', 'medicos.med_id = consultas.med_id')
        ->join('personas', 'personas.per_id = medicos.med_id')
        ->where('pacientes.pac_id', $pacId)
        ->findAll();
    }

    // Obtener consulta por ID
    public function getConsultaById($id)
    {
        return $this->select('*')
            ->where('con_id', $id)
            ->first();
    }
}
