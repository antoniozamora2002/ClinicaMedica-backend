<?php

namespace App\Models;

use CodeIgniter\Model;

class TriajeModel extends Model
{
    protected $table = 'triaje';
    protected $primaryKey = 'tri_id';

    protected $allowedFields = [
        'con_id',
        'usu_id',
        'da_id',
        'tri_peso_kg',
        'tri_talla_cm',
        'tri_presion_arterial',
        'tri_temperatura',
        'tri_frecuencia_cardiaca',
        'tri_saturacion_o2',
        'tri_fecha_hora',
        'tri_imc'
    ];

    public function getTriajePorConsulta($conId)
    {
        return $this->where('con_id', $conId)->first();
    }
}
