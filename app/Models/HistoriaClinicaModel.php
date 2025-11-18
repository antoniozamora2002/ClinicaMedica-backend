<?php

namespace App\Models;

use CodeIgniter\Model;

class HistoriaClinicaModel extends Model
{
    protected $table      = 'historias_clinicas';
    protected $primaryKey = 'his_id';

    protected $allowedFields = [
        'pac_id',
        'his_codigo',
        'his_ant_personales',
        'his_ant_familiares',
        'his_alergias',
        'his_enfermedades_cronicas',
        'his_notas_relevantes',
        'his_fecha_creacion'
    ];

    // Obtener la historia clínica por paciente
    public function getHistoriaClinicaByPaciente($pacId)
    {
        return $this->select('
            historias_clinicas.his_id,
            historias_clinicas.his_codigo,
            historias_clinicas.his_ant_personales,
            historias_clinicas.his_ant_familiares,
            historias_clinicas.his_alergias,
            historias_clinicas.his_enfermedades_cronicas,
            historias_clinicas.his_notas_relevantes,
            historias_clinicas.his_fecha_creacion
        ')
        ->join('pacientes', 'pacientes.pac_id = historias_clinicas.pac_id')
        ->where('pacientes.pac_id', $pacId)
        ->first();
    }
}
