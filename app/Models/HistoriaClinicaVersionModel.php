<?php

namespace App\Models;

use CodeIgniter\Model;

class HistoriaClinicaVersionModel extends Model
{
    protected $table = 'historias_clinicas_versiones';
    protected $primaryKey = 'hiv_id';

    protected $allowedFields = [
        'his_id',
        'hiv_version',
        'hiv_fecha',
        'hiv_actualizado_por',
        'hiv_motivo',
        'hiv_ant_personales',
        'hiv_ant_familiares',
        'hiv_alergias',
        'hiv_enfermedades_cronicas',
        'hiv_notas_relevantes'
    ];
}
