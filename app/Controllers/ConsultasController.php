<?php

namespace App\Controllers;

use CodeIgniter\RESTful\ResourceController;
use App\Models\ConsultasModel;
use App\Models\HistoriaClinicaModel;

class ConsultasController extends ResourceController
{
    protected $format = 'json';

    // ============================================================
    // LISTAR CONSULTAS DE UN PACIENTE
    // ============================================================
    public function index($pacId = null)
    {
        if (!$pacId) {
            return $this->failNotFound("ID de paciente es necesario.");
        }

        $model = new ConsultasModel();
        $consultas = $model->getConsultasByPaciente($pacId);

        if (!$consultas) {
            return $this->failNotFound("No se encontraron consultas para este paciente.");
        }

        return $this->respond($consultas);
    }

    // ============================================================
    // OBTENER CONSULTA POR ID
    // ============================================================
    public function show($id = null)
    {
        if (!$id) {
            return $this->failNotFound("ID de consulta es necesario.");
        }

        $model = new ConsultasModel();
        $consulta = $model->getConsultaById($id);

        if (!$consulta) {
            return $this->failNotFound("Consulta no encontrada.");
        }

        return $this->respond($consulta);
    }

    // ============================================================
    // REGISTRAR CONSULTA
    // ============================================================
    public function create()
    {
        $json = $this->request->getJSON(true);

        // Validación de campos
        if (empty($json['pac_id']) || empty($json['med_id']) || empty($json['con_fecha_consulta'])) {
            return $this->respond([
                'status' => 400,
                'message' => 'Faltan datos importantes (pac_id, med_id, con_fecha_consulta)'
            ], 400);
        }

        // Insertar consulta
        $model = new ConsultasModel();
        $model->insert([
            'his_id'                => $json['his_id'],
            'med_id'                => $json['med_id'],
            'tip_id'                => $json['tip_id'],
            'ase_id'                => $json['ase_id'],
            'con_fecha_consulta'    => $json['con_fecha_consulta'],
            'con_tipo_servicio'     => $json['con_tipo_servicio'],
            'con_area'              => $json['con_area'],
            'con_num_poliza'        => $json['con_num_poliza'],
            'con_razon_social'      => $json['con_razon_social'],
            'con_ocupacion'         => $json['con_ocupacion'],
            'con_turno'             => $json['con_turno'],
            'con_horas_extra'       => $json['con_horas_extra'],
            'con_enf_cronicas'      => $json['con_enf_cronicas'],
            'con_alergias'          => $json['con_alergias'],
            'con_medicamentos_actuales' => $json['con_medicamentos_actuales'],
            'con_antecedentes_quirurgicos' => $json['con_antecedentes_quirurgicos'],
            'con_habitos_nocivos'   => $json['con_habitos_nocivos'],
            'con_habitos_frecuencia' => $json['con_habitos_frecuencia'],
            'con_sintomas'          => $json['con_sintomas'],
            'con_tiempo_enfermedad' => $json['con_tiempo_enfermedad'],
            'con_motivo_consulta'   => $json['con_motivo_consulta'],
            'con_diagnostico'       => $json['con_diagnostico'],
            'con_tratamiento'       => $json['con_tratamiento'],
            'con_receta'            => $json['con_receta'],
            'con_observaciones'     => $json['con_observaciones'],
            'con_cie10_principal'   => $json['con_cie10_principal']
        ]);

        return $this->respondCreated([
            'status' => 201,
            'message' => 'Consulta registrada exitosamente.'
        ]);
    }
}
