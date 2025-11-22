<?php

namespace App\Models;

use CodeIgniter\Model;

class PacientesModel extends Model
{
    protected $table      = 'pacientes';
    protected $primaryKey = 'pac_id';

    // Importante: pac_id NO es autoincrement
    protected $useAutoIncrement = false; 

    protected $returnType = 'array';

    protected $allowedFields = [
        'pac_id',
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
        'pac_estado'
    ];

    // ===================================================
    // LISTAR PACIENTES ACTIVOS
    // ===================================================
    public function getPacientes()
    {
        return $this->select('personas.*, pacientes.*')
            ->join('personas', 'personas.per_id = pacientes.pac_id')
            ->where('personas.per_estado', 'ACTIVO')
            ->where('pacientes.pac_estado', 'ACTIVO')
            ->findAll();
    }

    // ===================================================
    // OBTENER PACIENTE POR ID
    // ===================================================
    public function getPacienteById($id)
    {
        return $this->select('personas.*, pacientes.*')
            ->join('personas', 'personas.per_id = pacientes.pac_id')
            ->where('pacientes.pac_id', $id)
            ->first();
    }
}
