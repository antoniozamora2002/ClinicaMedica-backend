<?php

namespace App\Controllers;

use CodeIgniter\RESTful\ResourceController;
use App\Models\TriajeModel;

class TriajeController extends ResourceController
{
    protected $format = 'json';

    public function create()
    {
        if (!userCan($this->request, 'TRIAJE', 'CREATE'))
            return $this->failForbidden("No tienes permiso.");

        $json = $this->request->getJSON(true);

        $model = new TriajeModel();

        // Insertar triaje
        $model->insert($json);
        $triId = $model->getInsertID();

        // Obtener triaje completo (triaje + paciente + persona)
        $triaje = $model->getTriajeCompleto($triId);

        return $this->respondCreated([
            "message" => "Triaje registrado correctamente",
            "tri_id"  => $triId,
            "data"    => $triaje
        ]);
    }

    public function show($id = null)
    {
        if (!userCan($this->request, 'TRIAJE', 'READ'))
            return $this->failForbidden("No tienes permiso.");

        $model = new TriajeModel();
        $data = $model->getTriajeCompleto($id);

        if (!$data)
            return $this->failNotFound("Triaje no encontrado.");

        return $this->respond($data);
    }
}
