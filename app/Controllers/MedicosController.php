<?php

namespace App\Controllers;

use CodeIgniter\RESTful\ResourceController;
use App\Models\MedicosModel;
use App\Models\EspecialidadesModel;
use App\Models\PersonasModel;
use App\Models\PacientesModel;

class MedicosController extends ResourceController
{
    protected $format = 'json';

    // ============================================
    // LISTAR MÉDICOS
    // ============================================
    public function index()
    {
        if (!userCan($this->request, 'MEDICOS', 'READ'))
            return $this->failForbidden("No tienes permiso para ver médicos.");

        $model = new MedicosModel();
        return $this->respond($model->getMedicos());
    }

    // ============================================
    // OBTENER POR ID
    // ============================================
    public function show($id = null)
    {
        if (!userCan($this->request, 'MEDICOS', 'READ'))
            return $this->failForbidden("No tienes permiso para consultar médicos.");

        $model = new MedicosModel();
        $data = $model->getMedicoById($id);

        if (!$data)
            return $this->failNotFound("Médico no encontrado.");

        return $this->respond($data);
    }

        // ============================================
    // BUSCAR POR DNI
    // ============================================
    public function buscarPorDni()
    {
        if (!userCan($this->request, 'MEDICOS', 'READ'))
            return $this->failForbidden("No tienes permiso para buscar médicos.");

        $dni = $this->request->getGet('dni');

        if (!$dni)
            return $this->failValidationErrors("Debe enviar ?dni=xxxxx");

        $model = new MedicosModel();
        $data = $model->buscarPorDni($dni);

        if (!$data)
            return $this->failNotFound("No se encontró un médico con este DNI.");

        return $this->respond($data);
    }

    // ============================================
    // BUSCAR POR APELLIDOS
    // ============================================
    public function buscarPorApellidos()
    {
        if (!userCan($this->request, 'MEDICOS', 'READ'))
            return $this->failForbidden("No tienes permiso para buscar médicos.");

        $paterno = $this->request->getGet('paterno');
        $materno = $this->request->getGet('materno');

        if (!$paterno)
            return $this->failValidationErrors("Debe enviar ?paterno=XXXX");

        $model = new MedicosModel();
        $data = $model->buscarPorApellidos($paterno, $materno);

        if (!$data)
            return $this->failNotFound("No se encontraron médicos con esos apellidos.");

        return $this->respond($data);
    }


    // ============================================
    // REGISTRAR MÉDICO
    // ============================================
    public function create()
    {
        if (!userCan($this->request, 'MEDICOS', 'CREATE'))
            return $this->failForbidden("No puedes registrar médicos.");
    
        $json = $this->request->getJSON(true);
        if (!$json)
            return $this->failValidationErrors("El JSON enviado es inválido o está vacío.");
    
        $modelPer = new PersonasModel();
        $modelMed = new MedicosModel();
        $db = \Config\Database::connect();
    
        // ==========================================================
        // 1. VALIDAR SI LA PERSONA YA EXISTE
        // ==========================================================
        $persona = $modelPer->where([
            'per_tipo_documento_id' => $json['tipo_documento_id'],
            'per_numero_documento'  => $json['numero_documento']
        ])->first();
    
        // Si PERSONA ya existe
        if ($persona) {
    
            $perId = $persona['per_id'];
    
            // Verificar si ya es médico
            if ($modelMed->find($perId)) {
                return $this->failResourceExists("Esta persona ya está registrada como médico.");
            }
    
            // Insertar MÉDICO usando persona existente
            $modelMed->insert([
                'med_id'             => $perId,
                'med_profesion'      => $json['profesion'],
                'med_colegiatura'    => $json['colegiatura'],
                'med_habilitacion'   => $json['habilitacion'],
                'med_cargo'          => $json['cargo'] ?? null,
                'med_otros_estudios' => $json['otros_estudios'] ?? null,
                'usu_id'             => $json['usu_id'],
                'med_estado'         => 'ACTIVO'
            ]);
    
            // Registrar ESPECIALIDADES si vienen
            if (!empty($json['especialidades']) && is_array($json['especialidades'])) {
                $this->registrarEspecialidades($db, $perId, $json['especialidades']);
            }
    
            return $this->respondCreated([
                "message" => "Médico registrado usando persona existente.",
                "med_id"  => $perId
            ]);
        }
    
        // ==========================================================
        // 2. PERSONA NO EXISTE → CREAR PERSONA + MÉDICO
        // ==========================================================
        $perId = $modelPer->insert([
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
            'per_ubigeo_nacimiento' => $json['ubigeo_nacimiento'] ?? null,
            'per_ubigeo_actual'     => $json['ubigeo_actual'] ?? null,
            'per_estado'            => 'ACTIVO'
        ]);
    
        if (!$perId)
            return $this->failServerError("Error al registrar la persona.");
    
        // Registrar MÉDICO
        $modelMed->insert([
            'med_id'             => $perId,
            'med_profesion'      => $json['profesion'],
            'med_colegiatura'    => $json['colegiatura'],
            'med_habilitacion'   => $json['habilitacion'],
            'med_cargo'          => $json['cargo'] ?? null,
            'med_otros_estudios' => $json['otros_estudios'] ?? null,
            'usu_id'             => $json['usu_id'],
            'med_estado'         => 'ACTIVO'
        ]);
    
        // Registrar ESPECIALIDADES
        if (!empty($json['especialidades']) && is_array($json['especialidades'])) {
            $this->registrarEspecialidades($db, $perId, $json['especialidades']);
        }
    
        return $this->respondCreated([
            "message" => "Médico registrado exitosamente",
            "med_id"  => $perId
        ]);
    }
    
    private function registrarEspecialidades($db, $medId, $especialidades)
    {
        $batch = [];

        foreach ($especialidades as $espId) {
            $batch[] = [
                'med_id'    => $medId,
                'esp_id'    => $espId,
                'me_estado' => 'ACTIVO'
            ];
        }

        $db->table('medicos_especialidad')->insertBatch($batch);
    }



    // ============================================
    // ACTUALIZAR MÉDICO
    // ============================================
    public function update($id = null)
    {
        if (!userCan($this->request, 'MEDICOS', 'UPDATE'))
            return $this->failForbidden("No puedes editar médicos.");

        $json = $this->request->getJSON(true);

        $modelMed = new MedicosModel();
        $modelPer = new PersonasModel();

        $med = $modelMed->getMedicoById($id);

        if (!$med)
            return $this->failNotFound("Médico no encontrado.");

        // ================================
        // 1. Actualizar PERSONA
        // ================================
        $modelPer->update($id, [
            'per_nombres'          => $json['nombres'],
            'per_apellido_paterno' => $json['apellido_paterno'],
            'per_apellido_materno' => $json['apellido_materno'],
            'per_fecha_nacimiento' => $json['fecha_nacimiento'],
            'per_sexo'             => $json['sexo'],
            'per_telefono'         => $json['telefono'],
            'per_correo'           => $json['correo'],
            'per_estado_civil'     => $json['estado_civil'],
            'per_nacionalidad'     => $json['nacionalidad']
        ]);

        // ================================
        // 2. Actualizar MÉDICO
        // ================================
        $modelMed->update($id, [
            'med_profesion'      => $json['profesion'],
            'med_colegiatura'    => $json['colegiatura'],
            'med_habilitacion'   => $json['habilitacion'],
            'med_cargo'          => $json['cargo'],
            'med_otros_estudios' => $json['otros_estudios']
        ]);

        // ================================
        // 3. Actualizar especialidades
        // ================================
        if (isset($json['especialidades'])) {
            $db = \Config\Database::connect();

            // Eliminar las actuales
            $db->table('medicos_especialidades')->where('med_id', $id)->delete();

            // Registrar las nuevas
            $batch = [];
            foreach ($json['especialidades'] as $espId) {
                $batch[] = [
                    'med_id'    => $id,
                    'esp_id'    => $espId,
                    'me_estado' => 'ACTIVO'
                ];
            }

            $db->table('medicos_especialidades')->insertBatch($batch);
        }

        return $this->respond([
            'message' => 'Médico actualizado correctamente',
            'medico_actualizado' => $id
        ]);
    }

    // ============================================
    // ELIMINAR MÉDICO (LÓGICO)
    // ============================================
    public function delete($id = null)
    {
        if (!userCan($this->request, 'MEDICOS', 'DELETE'))
            return $this->failForbidden("No puedes eliminar médicos.");

        $modelMed = new MedicosModel();

        $med = $modelMed->getMedicoById($id);
        if (!$med)
            return $this->failNotFound("Médico no encontrado.");

        if ($med['med_estado'] === 'INACTIVO')
            return $this->failResourceGone("El médico ya está eliminado.");

        $modelMed->update($id, ['med_estado' => 'INACTIVO']);

        return $this->respondDeleted([
            'message' => 'Médico eliminado correctamente',
            'medico_eliminado' => $id
        ]);
    }
}
