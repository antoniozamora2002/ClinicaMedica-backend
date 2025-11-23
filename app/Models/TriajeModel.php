<?php

namespace App\Models;

use CodeIgniter\Model;

class TriajeModel extends Model
{
    protected $table      = 'triaje';
    protected $primaryKey = 'tri_id';

    protected $allowedFields = [
        'con_id',
        'usu_id',
        'cla_id',
        'tri_peso_kg',
        'tri_talla_cm',
        'tri_presion_arterial',
        'tri_temperatura',
        'tri_frecuencia_cardiaca',
        'tri_saturacion_o2',
        'tri_imc',
        'tri_fecha_hora'
    ];

    // Obtener triaje por consulta
    public function getTriajeByConsulta($conId)
    {
        return $this->select('*')
            ->where('con_id', $conId)
            ->first();
    }

    // Obtener todos los registros de triaje
    public function getTriajes()
    {
        return $this->findAll();
    }
}
