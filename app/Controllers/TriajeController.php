<?php

namespace App\Controllers;

use CodeIgniter\RESTful\ResourceController;
use App\Models\TriajeModel;

class TriajeController extends ResourceController
{
    protected $format = 'json';

    // REGISTRAR TRIAJE
    public function create()
    {
        if (!userCan($this->request, 'TRIAJE', 'CREATE'))
            return $this->failForbidden("No tienes permiso.");

        $json = $this->request->getJSON(true);

        $model = new TriajeModel();
        $model->insert($json);

        return $this->respondCreated([
            "message" => "Triaje registrado",
            "tri_id"  => $model->getInsertID()
        ]);
    }

    // OBTENER TRIAJE POR CONSULTA
    public function show($conId = null)
    {
        if (!userCan($this->request, 'TRIAJE', 'READ'))
            return $this->failForbidden("No tienes permiso.");

        $model = new TriajeModel();
        $data = $model->getTriajePorConsulta($conId);

        if (!$data)
            return $this->failNotFound("Triaje no encontrado.");

        return $this->respond($data);
    }
}
