<?php

namespace App\Models;

use CodeIgniter\Model;

class PacientesModel extends Model
{
    protected $table      = 'pacientes';
    protected $primaryKey = 'pac_id';
    protected $useAutoIncrement = false; 

    protected $allowedFields = [
        'pac_lugar_nac_dep',
        'pac_lugar_nac_prov',
        'pac_lugar_nac_dist',
        'pac_departamento',
        'pac_provincia',
        'pac_distrito',
        'pac_direccion',
        'pac_celular_emergencia',
        'pac_nombre_emergencia',
        'pac_parentesco_emergencia',
        'pac_ocupacion',
        'pac_observaciones'
    ];

    public function getPacientes()
    {
        return $this->select('
            personas.*,
            pacientes.*
        ')
        ->join('personas', 'personas.per_id = pacientes.pac_id')
        ->where('personas.per_estado', 'ACTIVO')
        ->findAll();
    }

    public function getPacienteById($id)
    {
        return $this->select('
            personas.*,
            pacientes.*
        ')
        ->join('personas', 'personas.per_id = pacientes.pac_id')
        ->where('pacientes.pac_id', $id)
        ->first();
    }
}
