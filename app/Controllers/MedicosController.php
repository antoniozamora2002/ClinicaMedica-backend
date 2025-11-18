<?php

namespace App\Controllers;

use CodeIgniter\RESTful\ResourceController;
use App\Models\MedicosModel;
use App\Models\EspecialidadesModel;
use App\Models\PersonasModel;

class MedicosController extends ResourceController
{
    protected $format = 'json';

    // ============================================
    // LISTAR MÉDICOS
    // ============================================
    public function index()
    {
        $model = new MedicosModel();
        return $this->respond($model->getMedicos());
    }

    // ============================================
    // OBTENER MÉDICO POR ID
    // ============================================
    public function show($id = null)
    {
        $model = new MedicosModel();
        $data = $model->getMedicoById($id);

        if (!$data)
            return $this->failNotFound("Médico no encontrado.");

        return $this->respond($data);
    }

    // ============================================
    // REGISTRAR MÉDICO
    // ============================================
    public function create()
    {
        $json = $this->request->getJSON(true);

        $personasModel = new PersonasModel();
        $medicosModel = new MedicosModel();

        // 1. Registrar Persona
        $perId = $personasModel->insert([
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

        // 2. Registrar Médico
        $medicosModel->insert([
            'med_id'        => $perId,  // Asignar per_id como med_id
            'med_profesion' => $json['profesion'],
            'med_colegiatura'=> $json['colegiatura'],
            'med_habilitacion'=> $json['habilitacion'],
            'med_cargo'     => $json['cargo'],
            'med_otros_estudios'=> $json['otros_estudios'],
            'med_estado'    => 'ACTIVO',
            'usu_id'        => $json['usu_id']  // Si tienes usuarios vinculados
        ]);

        // 3. Asignar especialidades
        if (isset($json['especialidades'])) {
            $especialidadesModel = new EspecialidadesModel();
            $medicoEspecialidades = [];
            foreach ($json['especialidades'] as $espId) {
                $medicoEspecialidades[] = [
                    'med_id' => $perId, 
                    'esp_id' => $espId,
                    'me_estado' => 'ACTIVO'
                ];
            }

            // Insertar especialidades
            $db = \Config\Database::connect();
            $db->table('medicos_especialidades')->insertBatch($medicoEspecialidades);
        }

        return $this->respondCreated([
            'message' => 'Médico registrado exitosamente',
            'data'    => $json
        ]);
    }

    // ============================================
    // ACTUALIZAR MÉDICO
    // ============================================
    public function update($id = null)
    {
        $json = $this->request->getJSON(true);

        $model = new MedicosModel();
        $medico = $model->getMedicoById($id);

        if (!$medico) {
            return $this->failNotFound("Médico no encontrado.");
        }

        $personasModel = new PersonasModel();

        // Actualizar persona
        $personasModel->update($id, [
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

        // Actualizar médico
        $model->update($id, [
            'med_profesion' => $json['profesion'],
            'med_colegiatura' => $json['colegiatura'],
            'med_habilitacion' => $json['habilitacion'],
            'med_cargo' => $json['cargo'],
            'med_otros_estudios' => $json['otros_estudios']
        ]);

        // Actualizar especialidades (si es necesario)
        if (isset($json['especialidades'])) {
            $db = \Config\Database::connect();
            $db->table('medicos_especialidades')->where('med_id', $id)->delete();

            $medicoEspecialidades = [];
            foreach ($json['especialidades'] as $espId) {
                $medicoEspecialidades[] = [
                    'med_id' => $id,
                    'esp_id' => $espId,
                    'me_estado' => 'ACTIVO'
                ];
            }

            $db->table('medicos_especialidades')->insertBatch($medicoEspecialidades);
        }

        return $this->respond([
            'message' => 'Médico actualizado correctamente'
        ]);
    }

    // ============================================
    // ELIMINAR MÉDICO (LÓGICAMENTE)
    // ============================================
    public function delete($id = null)
    {
        $model = new MedicosModel();
        $medico = $model->getMedicoById($id);

        if (!$medico) {
            return $this->failNotFound("Médico no encontrado.");
        }

        // Eliminar (logicamente) médico
        $model->update($id, ['med_estado' => 'INACTIVO']);

        return $this->respondDeleted([
            'message' => 'Médico eliminado correctamente'
        ]);
    }
}
