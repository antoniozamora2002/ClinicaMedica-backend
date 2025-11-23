<?php

namespace App\Controllers;

use CodeIgniter\RESTful\ResourceController;
use App\Models\PersonasModel;
use App\Models\PacientesModel;

class PacientesController extends ResourceController
{
    protected $format = 'json';

    // ============================================================
    // LISTAR
    // ============================================================
    public function index()
    {
        if (!userCan($this->request, 'PACIENTES', 'READ'))
            return $this->failForbidden("No tienes permiso para ver pacientes.");

        $model = new PacientesModel();
        return $this->respond($model->getPacientes());
    }

    // ============================================================
    // OBTENER POR ID
    // ============================================================
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

    // ============================================================
    // REGISTRAR PACIENTE
    // ============================================================
    public function create()
    {
        if (!userCan($this->request, 'PACIENTES', 'CREATE'))
            return $this->failForbidden("No puedes registrar pacientes.");

        $json = $this->request->getJSON(true);

        if (!$json)
            return $this->failValidationErrors("El JSON enviado es inválido o está vacío.");

        // ====================================================
        // VALIDAR DOCUMENTO DUPLICADO ANTES DE INSERTAR
        // ====================================================
        $pers = new PersonasModel();

        $existe = $pers->where([
            'per_tipo_documento_id' => $json['tipo_documento_id'],
            'per_numero_documento'  => $json['numero_documento']
        ])->first();

        if ($existe) {
            return $this->failResourceExists(
                "El número de documento ya se encuentra registrado."
            );
        }

        // ====================================================
        // 1. Crear PERSONA
        // ====================================================
        $perId = $pers->insert([
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
            return $this->failServerError("Error al registrar la persona.");

        // ====================================================
        // 2. Crear PACIENTE
        // ====================================================
        $pac = new PacientesModel();

        $pac->insert([
            'pac_id'                 => $perId,
            'pac_lugar_nac_dep'      => $json['lugar_nac_dep'],
            'pac_lugar_nac_prov'     => $json['lugar_nac_prov'],
            'pac_lugar_nac_dist'     => $json['lugar_nac_dist'],
            'pac_departamento'       => $json['departamento'],
            'pac_provincia'          => $json['provincia'],
            'pac_distrito'           => $json['distrito'],
            'pac_direccion'          => $json['direccion'],
            'pac_celular_emergencia' => $json['celular_emergencia'],
            'pac_nombre_emergencia'  => $json['nombre_emergencia'],
            'pac_parentesco_emergencia' => $json['parentesco_emergencia'],
            'pac_ocupacion'          => $json['ocupacion'],
            'pac_estado'             => 'ACTIVO'
        ]);

        return $this->respondCreated([
            'message' => 'Paciente registrado exitosamente',
            'pac_id'  => $perId
        ]);
    }


    // ============================================================
    // UPDATE
    // ============================================================
    public function update($id = null)
    {
        if (!userCan($this->request, 'PACIENTES', 'UPDATE'))
            return $this->failForbidden("No puedes editar pacientes.");

        $json = $this->request->getJSON(true);

        $modelPac = new PacientesModel();
        $modelPer = new PersonasModel();

        // Verificar existencia
        $pac = $modelPac->getPacienteById($id);
        if (!$pac)
            return $this->failNotFound("Paciente no encontrado.");

        // =======================
        // 1. UPDATE PERSONA
        // =======================
        $personaData = array_filter([
            'per_tipo_documento_id' => $json['tipo_documento_id'] ?? null,
            'per_numero_documento'  => $json['numero_documento'] ?? null,
            'per_nombres'           => $json['nombres'] ?? null,
            'per_apellido_paterno'  => $json['apellido_paterno'] ?? null,
            'per_apellido_materno'  => $json['apellido_materno'] ?? null,
            'per_fecha_nacimiento'  => $json['fecha_nacimiento'] ?? null,
            'per_sexo'              => $json['sexo'] ?? null,
            'per_telefono'          => $json['telefono'] ?? null,
            'per_correo'            => $json['correo'] ?? null,
            'per_estado_civil'      => $json['estado_civil'] ?? null,
            'per_nacionalidad'      => $json['nacionalidad'] ?? null
        ]);

        if ($personaData)
            $modelPer->update($id, $personaData);

        // =======================
        // 2. UPDATE PACIENTE
        // =======================
        $pacData = array_filter([
            'pac_lugar_nac_dep'         => $json['lugar_nac_dep'] ?? null,
            'pac_lugar_nac_prov'        => $json['lugar_nac_prov'] ?? null,
            'pac_lugar_nac_dist'        => $json['lugar_nac_dist'] ?? null,
            'pac_departamento'          => $json['departamento'] ?? null,
            'pac_provincia'             => $json['provincia'] ?? null,
            'pac_distrito'              => $json['distrito'] ?? null,
            'pac_direccion'             => $json['direccion'] ?? null,
            'pac_celular_emergencia'    => $json['celular_emergencia'] ?? null,
            'pac_nombre_emergencia'     => $json['nombre_emergencia'] ?? null,
            'pac_parentesco_emergencia' => $json['parentesco_emergencia'] ?? null,
            'pac_ocupacion'             => $json['ocupacion'] ?? null,
        ]);

        if ($pacData)
            $modelPac->update($id, $pacData);

        return $this->respond([
            'message' => 'Paciente actualizado exitosamente',
            'id' => $id
        ]);
    }

    // ============================================================
    // DELETE LÓGICO
    // ============================================================
    public function delete($id = null)
    {
        if (!userCan($this->request, 'PACIENTES', 'DELETE'))
            return $this->failForbidden("No puedes eliminar pacientes.");

        if ($id === null)
            return $this->failValidationError("Debes indicar un ID de paciente.");

        $modelPac = new PacientesModel();
        $modelPer = new PersonasModel();

        // 1. Obtener paciente
        $pac = $modelPac->getPacienteById($id);
        if (!$pac) {
            return $this->failNotFound("Paciente no encontrado.");
        }

        // 2. Verificar si YA está eliminado (solo paciente)
        if ($pac['pac_estado'] === 'INACTIVO') {
            return $this->failResourceGone("El paciente ya está eliminado.");
        }

        // 3. Marcar SOLO PACIENTE como INACTIVO
        //    No tocar personas.per_estado porque podría ser médico o usuario activo.
        $modelPac->update($id, [
            'pac_estado' => 'INACTIVO'
        ]);

        return $this->respondDeleted([
            'message' => 'Paciente eliminado correctamente',
            'paciente_eliminado' => $id
        ]);
    }
}
