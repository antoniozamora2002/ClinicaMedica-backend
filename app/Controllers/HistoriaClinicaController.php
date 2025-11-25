<?php

namespace App\Controllers;

use CodeIgniter\RESTful\ResourceController;
use App\Models\HistoriaClinicaModel;
use App\Models\HistoriaClinicaVersionModel;

class HistoriaClinicaController extends ResourceController
{
    protected $format = 'json';

    // ============================================================
    // OBTENER HISTORIA CLÍNICA POR PACIENTE
    // ============================================================
    public function show($pacId = null)
    {
        if (!$pacId) {
            return $this->failNotFound("ID de paciente es necesario.");
        }

        $model = new HistoriaClinicaModel();
        $historia = $model->where('pac_id', $pacId)->first();

        if (!$historia) {
            return $this->failNotFound("Historia clínica no encontrada.");
        }

        return $this->respond($historia);
    }

    // ============================================================
    // CREAR HISTORIA CLÍNICA
    // ============================================================
    public function create()
    {
        $json = $this->request->getJSON(true);

        if (!$json || !isset($json['pac_id'])) {
            return $this->failValidationErrors("Datos inválidos o incompletos.");
        }

        $model = new HistoriaClinicaModel();

        // Evitar duplicado
        $existe = $model->where('pac_id', $json['pac_id'])->first();
        if ($existe) {
            return $this->failResourceExists("El paciente ya tiene historia clínica.");
        }

        $data = [
            'pac_id'                    => $json['pac_id'],
            'his_codigo'                => $json['his_codigo'] ?? null,
            'his_ant_personales'        => $json['his_ant_personales'] ?? null,
            'his_ant_familiares'        => $json['his_ant_familiares'] ?? null,
            'his_alergias'              => $json['his_alergias'] ?? null,
            'his_enfermedades_cronicas' => $json['his_enfermedades_cronicas'] ?? null,
            'his_notas_relevantes'      => $json['his_notas_relevantes'] ?? null,
            'his_fecha_creacion'        => date('Y-m-d H:i:s')
        ];

        $model->insert($data);
        $hisId = $model->getInsertID();

        return $this->respondCreated([
            'message' => 'Historia clínica creada correctamente.',
            'his_id'  => $hisId
        ]);
    }

    // ============================================================
    // ACTUALIZAR HISTORIA CLÍNICA (trigger genera versión)
    // ============================================================
    public function update($hisId = null)
    {
        if (!$hisId) {
            return $this->failValidationErrors("ID de historia clínica requerido.");
        }

        $json = $this->request->getJSON(true);

        $model = new HistoriaClinicaModel();
        $hist = $model->find($hisId);

        if (!$hist) {
            return $this->failNotFound("Historia clínica no existe.");
        }

        $updateData = [
            'his_ant_personales'        => $json['his_ant_personales'] ?? $hist['his_ant_personales'],
            'his_ant_familiares'        => $json['his_ant_familiares'] ?? $hist['his_ant_familiares'],
            'his_alergias'              => $json['his_alergias'] ?? $hist['his_alergias'],
            'his_enfermedades_cronicas' => $json['his_enfermedades_cronicas'] ?? $hist['his_enfermedades_cronicas'],
            'his_notas_relevantes'      => $json['his_notas_relevantes'] ?? $hist['his_notas_relevantes'],
        ];

        $model->update($hisId, $updateData);

        return $this->respond([
            'message' => 'Historia clínica actualizada correctamente (versión generada).',
            'his_id'  => $hisId
        ]);
    }

    // ============================================================
    // LISTAR VERSIONES
    // ============================================================
    public function versiones($hisId = null)
    {
        if (!$hisId) {
            return $this->failValidationErrors("ID de historia clínica es necesario.");
        }

        $model = new HistoriaClinicaVersionModel();
        $data = $model->where('his_id', $hisId)->orderBy('hiv_version', 'DESC')->findAll();

        return $this->respond($data);
    }

    // ============================================================
    // OBTENER UNA VERSIÓN ESPECÍFICA
    // ============================================================
    public function version($hivId = null)
    {
        if (!$hivId) {
            return $this->failValidationErrors("ID de versión es necesario.");
        }

        $model = new HistoriaClinicaVersionModel();
        $data = $model->find($hivId);

        if (!$data) {
            return $this->failNotFound("Versión no encontrada.");
        }

        return $this->respond($data);
    }
}
