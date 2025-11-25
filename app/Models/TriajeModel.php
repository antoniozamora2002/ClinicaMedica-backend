<?php

namespace App\Models;

use CodeIgniter\Model;

class TriajeModel extends Model
{
    protected $table = 'triaje';
    protected $primaryKey = 'tri_id';
    protected $returnType = 'array';

    protected $allowedFields = [
        'pac_id',
        'usu_id',
        'cla_id',
        'tri_peso_kg',
        'tri_talla_cm',
        'tri_presion_arterial',
        'tri_temperatura',
        'tri_frecuencia_cardiaca',
        'tri_saturacion_o2',
        'tri_fecha_hora'
    ];

    public function getTriajeCompleto($triId)
    {
        return $this->select("triaje.*, pacientes.*, personas.*")
            ->join("pacientes", "pacientes.pac_id = triaje.pac_id")
            ->join("personas", "personas.per_id = pacientes.pac_id")
            ->where("triaje.tri_id", $triId)
            ->first();
    }
}
