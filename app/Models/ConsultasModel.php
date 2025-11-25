<?php

namespace App\Models;

use CodeIgniter\Model;

class ConsultasModel extends Model
{
    protected $table = 'consultas';
    protected $primaryKey = 'con_id';
    protected $returnType = 'array';

    protected $allowedFields = [
        'tri_id',
        'med_id',
        'esp_id',
        'tip_id',
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

    // ==============================================
    // CONSULTAS POR PACIENTE
    // ==============================================
    public function getConsultasPorPaciente($pacId)
    {
        return $this->select("consultas.*, triaje.*, pacientes.*, personas.*")
            ->join("triaje", "triaje.tri_id = consultas.tri_id")
            ->join("pacientes", "pacientes.pac_id = triaje.pac_id")
            ->join("personas", "personas.per_id = pacientes.pac_id")
            ->where("pacientes.pac_id", $pacId)
            ->orderBy("consultas.con_fecha_consulta", "DESC")
            ->findAll();
    }

    // ==============================================
    // CONSULTA POR ID
    // ==============================================
    public function getConsultaById($id)
    {
        return $this->select("consultas.*, triaje.*, pacientes.*, personas.*")
            ->join("triaje", "triaje.tri_id = consultas.tri_id")
            ->join("pacientes", "pacientes.pac_id = triaje.pac_id")
            ->join("personas", "personas.per_id = pacientes.pac_id")
            ->where("consultas.con_id", $id)
            ->first();
    }

    // ==============================================
    // VALIDAR SI EL TRIAJE YA TIENE CONSULTA
    // ==============================================
    public function existeConsultaParaTriaje($tri_id)
    {
        return $this->where("tri_id", $tri_id)->first();
    }
}
