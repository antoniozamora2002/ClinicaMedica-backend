<?php

namespace App\Controllers;

use CodeIgniter\RESTful\ResourceController;
use App\Models\HistoriaClinicaModel;

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
        $historia = $model->getHistoriaClinicaByPaciente($pacId);

        if (!$historia) {
            return $this->failNotFound("Historia clínica no encontrada.");
        }

        return $this->respond($historia);
    }
}
