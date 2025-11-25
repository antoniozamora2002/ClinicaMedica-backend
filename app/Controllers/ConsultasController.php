<?php

namespace App\Controllers;

use CodeIgniter\RESTful\ResourceController;
use App\Models\ConsultasModel;

class ConsultasController extends ResourceController
{
    protected $format = 'json';

    // LISTAR CONSULTAS POR PACIENTE
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

    // OBTENER CONSULTA POR ID
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

    // CREAR CONSULTA
    public function create()
    {
        if (!userCan($this->request, 'CONSULTAS', 'CREATE'))
            return $this->failForbidden("No tienes permiso.");

        $json = $this->request->getJSON(true);

        $model = new ConsultasModel();
        $model->insert($json);

        return $this->respondCreated([
            "message" => "Consulta registrada correctamente",
            "con_id"  => $model->getInsertID()
        ]);
    }

    // ACTUALIZAR CONSULTA
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
