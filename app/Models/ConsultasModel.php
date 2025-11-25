<?php

namespace App\Models;

use CodeIgniter\Model;

class ConsultasModel extends Model
{
    protected $table = 'consultas';
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

    public function getConsultasPorPaciente($pacId)
    {
        return $this->select('consultas.*, historias_clinicas.his_codigo, personas.*')
            ->join('historias_clinicas', 'historias_clinicas.his_id = consultas.his_id')
            ->join('pacientes', 'pacientes.pac_id = historias_clinicas.pac_id')
            ->join('personas', 'personas.per_id = pacientes.pac_id')
            ->where('pacientes.pac_id', $pacId)
            ->findAll();
    }

    public function getConsultaById($conId)
    {
        return $this->where('con_id', $conId)->first();
    }
}
