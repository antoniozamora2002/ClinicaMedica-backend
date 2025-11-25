<?php

namespace App\Controllers;

use CodeIgniter\RESTful\ResourceController;
use App\Models\ConsultasModel;
use App\Models\TriajeModel;

class ConsultasController extends ResourceController
{
    protected $format = 'json';

    // ===========================================
    // LISTAR CONSULTAS POR PACIENTE
    // ===========================================
    public function index()
    {
        if (!userCan($this->request, 'CONSULTAS', 'READ'))
            return $this->failForbidden("No tienes permiso.");

        $pacId = $this->request->getGet('pac_id');
        if (!$pacId)
            return $this->failValidationErrors("Debe enviar pac_id");

        $model = new ConsultasModel();
        return $this->respond($model->getConsultasPorPaciente($pacId));
    }

    // ===========================================
    // OBTENER CONSULTA POR ID
    // ===========================================
    public function show($id = null)
    {
        if (!userCan($this->request, 'CONSULTAS', 'READ'))
            return $this->failForbidden("No tienes permiso.");

        $model = new ConsultasModel();
        $data = $model->getConsultaById($id);

        if (!$data)
            return $this->failNotFound("Consulta no existe.");

        return $this->respond($data);
    }

    // ===========================================
    // CREAR CONSULTA (RELACIÓN 1 A 1 CON TRIAJE)
    // ===========================================
    public function create()
    {
        if (!userCan($this->request, 'CONSULTAS', 'CREATE'))
            return $this->failForbidden("No tienes permiso.");

        $json = $this->request->getJSON(true);

        if (!isset($json["tri_id"]))
            return $this->failValidationErrors("Debe enviar tri_id");

        $triModel = new TriajeModel();
        $consultaModel = new ConsultasModel();

        // 1. Validar que el triaje exista
        $triaje = $triModel->find($json["tri_id"]);
        if (!$triaje)
            return $this->failNotFound("El triaje no existe.");

        // 2. Validar que ese triaje no tenga consulta aún
        if ($consultaModel->existeConsultaParaTriaje($json["tri_id"]))
            return $this->failResourceExists("El triaje ya tiene una consulta registrada.");

        // 3. Insertar consulta
        $consultaModel->insert($json);

        return $this->respondCreated([
            "message" => "Consulta registrada correctamente",
            "con_id" => $consultaModel->getInsertID()
        ]);
    }

    // ===========================================
    // ACTUALIZAR CONSULTA
    // ===========================================
    public function update($id = null)
    {
        if (!userCan($this->request, 'CONSULTAS', 'UPDATE'))
            return $this->failForbidden("No tienes permiso.");

        $json = $this->request->getJSON(true);

        $model = new ConsultasModel();
        $model->update($id, $json);

        return $this->respond([
            "message" => "Consulta actualizada",
            "con_id" => $id
        ]);
    }
}
