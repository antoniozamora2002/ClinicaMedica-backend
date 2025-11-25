<?php

namespace App\Models;

use CodeIgniter\Model;

class PacientesModel extends Model
{
    protected $table      = 'pacientes';
    protected $primaryKey = 'pac_id';
    protected $useAutoIncrement = false;


    protected $allowedFields = [
        'pac_id',
        'pac_direccion',
        'pac_celular_emergencia',
        'pac_nombre_emergencia',
        'pac_parentesco_emergencia',
        'pac_ocupacion',
        'pac_estado'
    ];

    // -------------------------
    // MÉTODOS PERSONALIZADOS
    // -------------------------

    public function getPacientes()
    {
        return $this->select('pacientes.*, personas.*')
            ->join('personas', 'personas.per_id = pacientes.pac_id')
            ->where('pacientes.pac_estado', 'ACTIVO')
            ->findAll();
    }

    public function getPacienteById($id)
    {
        return $this->select('pacientes.*, personas.*')
            ->join('personas', 'personas.per_id = pacientes.pac_id')
            ->where('pacientes.pac_id', $id)
            ->first();
    }

    public function buscarPorDocumento($numero)
    {
        return $this->select('pacientes.*, personas.*')
            ->join('personas', 'personas.per_id = pacientes.pac_id')
            ->where('personas.per_numero_documento', $numero)
            ->first();
    }

    public function buscarPorApellidos($paterno, $materno)
    {
        $this->select('pacientes.*, personas.*')
            ->join('personas', 'personas.per_id = pacientes.pac_id');

        if ($paterno)
            $this->like('personas.per_apellido_paterno', $paterno);

        if ($materno)
            $this->like('personas.per_apellido_materno', $materno);

        return $this->findAll();
    }
}
