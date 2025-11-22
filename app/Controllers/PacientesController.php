<?php

namespace App\Controllers;

use CodeIgniter\RESTful\ResourceController;
use App\Models\PersonasModel;
use App\Models\PacientesModel;

class PacientesController extends ResourceController
{
    protected $format = 'json';

    // ============================================
    // LISTAR
    // ============================================
    public function index()
    {
        $req = $this->request;

        if (!userCan($req, 'PACIENTES', 'READ'))
            return $this->failForbidden("No tienes permiso para ver pacientes.");

        $model = new PacientesModel();
        return $this->respond($model->getPacientes());
    }

    // ============================================
    // OBTENER POR ID
    // ============================================
    public function show($id = null)
    {
        if (!userCan($this->request, 'PACIENTES', 'READ'))
            return $this->failForbidden("No puedes consultar pacientes.");

        $model = new PacientesModel();
        $pac = $model->getPacienteById($id);

        if (!$pac)
            return $this->failNotFound("Paciente no encontrado");

        return $this->respond($pac);
    }

    // ============================================
    // REGISTRAR PACIENTE
    // ============================================
    public function create()
    {
        if (!userCan($this->request, 'PACIENTES', 'CREATE'))
            return $this->failForbidden("No puedes registrar pacientes.");
    
        $json = $this->request->getJSON(true);
    
        if (!$json)
            return $this->failValidationErrors("El JSON enviado es inválido o está vacío.");
    
        // ================================
        // 1. Insertar PERSONA
        // ================================
        $personas = new PersonasModel();
        $perId = $personas->insert([
            'per_tipo_documento_id' => $json['tipo_documento_id'],
            'per_numero_documento'  => $json['numero_documento'],
            'per_nombres'           => $json['nombres'],
            'per_apellido_paterno'  => $json['apellido_paterno'],
            'per_apellido_materno'  => $json['apellido_materno'],
            'per_fecha_nacimiento'  => $json['fecha_nacimiento'],
            'per_sexo'              => $json['sexo'],
            'per_telefono'          => $json['telefono'],
            'per_correo'            => $json['correo'],
            'per_estado_civil'      => $json['estado_civil'],
            'per_nacionalidad'      => $json['nacionalidad'],
            'per_estado'            => 'ACTIVO'
        ]);
            
        if (!$perId)
            return $this->failServerError("Error al registrar los datos de la persona.");
    
        // ================================
        // 2. Insertar PACIENTE (solo 1 insert)
        // ================================
        $pacientes = new PacientesModel();
        $pacientes->insert([
            'pac_id'                    => $perId,
            'pac_lugar_nac_dep'         => $json['lugar_nac_dep'],
            'pac_lugar_nac_prov'        => $json['lugar_nac_prov'],
            'pac_lugar_nac_dist'        => $json['lugar_nac_dist'],
            'pac_departamento'          => $json['departamento'],
            'pac_provincia'             => $json['provincia'],
            'pac_distrito'              => $json['distrito'],
            'pac_direccion'             => $json['direccion'],
            'pac_celular_emergencia'    => $json['celular_emergencia'],
            'pac_nombre_emergencia'     => $json['nombre_emergencia'],
            'pac_parentesco_emergencia' => $json['parentesco_emergencia'],
            'pac_ocupacion'             => $json['ocupacion'],
            'pac_observaciones'         => $json['observaciones'],
        ]);
    
        return $this->respondCreated([
            'message'  => 'Paciente registrado exitosamente',
            'pac_id'   => $perId
        ]);
    }
    
    
    
    // ============================================
    // UPDATE
    // ============================================
    public function update($id = null)
    {
        if (!userCan($this->request, 'PACIENTES', 'UPDATE'))
            return $this->failForbidden("No puedes editar pacientes.");
    
        $json = $this->request->getJSON(true);
    
        if (!$json)
            return $this->failValidationErrors("El JSON enviado es inválido o está vacío.");
    
        $model = new PacientesModel();
        $paciente = $model->getPacienteById($id);
    
        if (!$paciente)
            return $this->failNotFound("Paciente no encontrado.");
    
        // ================================
        // 1. Actualizar PERSONA
        // ================================
        $personas = new PersonasModel();
        $personas->update($id, [
            'per_tipo_documento_id' => $json['tipo_documento_id'],
            'per_numero_documento'  => $json['numero_documento'],
            'per_nombres'           => $json['nombres'],
            'per_apellido_paterno'  => $json['apellido_paterno'],
            'per_apellido_materno'  => $json['apellido_materno'],
            'per_fecha_nacimiento'  => $json['fecha_nacimiento'],
            'per_sexo'              => $json['sexo'],
            'per_telefono'          => $json['telefono'],
            'per_correo'            => $json['correo'],
            'per_estado_civil'      => $json['estado_civil'],
            'per_nacionalidad'      => $json['nacionalidad']
        ]);
    
        // ================================
        // 2. Actualizar PACIENTE
        // ================================
        $model->update($id, [
            'pac_lugar_nac_dep'         => $json['lugar_nac_dep'],
            'pac_lugar_nac_prov'        => $json['lugar_nac_prov'],
            'pac_lugar_nac_dist'        => $json['lugar_nac_dist'],
            'pac_departamento'          => $json['departamento'],
            'pac_provincia'             => $json['provincia'],
            'pac_distrito'              => $json['distrito'],
            'pac_direccion'             => $json['direccion'],
            'pac_celular_emergencia'    => $json['celular_emergencia'],
            'pac_nombre_emergencia'     => $json['nombre_emergencia'],
            'pac_parentesco_emergencia' => $json['parentesco_emergencia'],
            'pac_ocupacion'             => $json['ocupacion'],
            'pac_observaciones'         => $json['observaciones']
        ]);
    
        return $this->respond([
            'message' => 'Paciente actualizado exitosamente'
        ]);
    }
    

    // ============================================
    // ELIMINACIÓN LÓGICA
    // ============================================
    public function delete($id = null)
    {
        if (!userCan($this->request, 'PACIENTES', 'DELETE'))
            return $this->failForbidden("No puedes eliminar pacientes.");

        $pers = new PersonasModel();
        $pers->update($id, ['per_estado' => 'INACTIVO']);

        return $this->respondDeleted([
            'message' => 'Paciente eliminado correctamente'
        ]);
    }
}
