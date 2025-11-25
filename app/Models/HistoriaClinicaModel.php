<?php

namespace App\Models;

use CodeIgniter\Model;

class HistoriaClinicaModel extends Model
{
    protected $table = 'historias_clinicas';
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
}
