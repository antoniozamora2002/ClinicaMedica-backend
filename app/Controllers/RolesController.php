<?php

namespace App\Controllers;

use CodeIgniter\RESTful\ResourceController;
use App\Models\RolesModel;
use App\Models\UsuariosRolesModel;

class RolesController extends ResourceController
{
    protected $format = 'json';

    // ===============================
    // LISTAR ROLES
    // ===============================
    public function index()
    {
        $model = new RolesModel();
        return $this->respond($model->findAll());
    }

    // ===============================
    // ASIGNAR ROL A USUARIO
    // ===============================
    public function assignRole()
    {
        $json = $this->request->getJSON(true);

        if (!isset($json['usu_id']) || !isset($json['rol_id'])) {
            return $this->failValidationErrors("Debe enviar usu_id y rol_id.");
        }

        $model = new UsuariosRolesModel();

        // Verificar si ya tiene el rol
        $exists = $model->where([
            'usu_id' => $json['usu_id'],
            'rol_id' => $json['rol_id']
        ])->first();

        if ($exists) {
            return $this->failResourceExists("El usuario ya tiene este rol asignado.");
        }

        $model->insert([
            'usu_id' => $json['usu_id'],
            'rol_id' => $json['rol_id'],
            'ur_estado' => 'ACTIVO'
        ]);

        return $this->respondCreated([
            "message" => "Rol asignado correctamente"
        ]);
    }

    // ===============================
    // QUITAR ROL
    // ===============================
    public function removeRole()
    {
        $json = $this->request->getJSON(true);

        if (!isset($json['usu_id']) || !isset($json['rol_id'])) {
            return $this->failValidationErrors("Debe enviar usu_id y rol_id.");
        }

        $model = new UsuariosRolesModel();

        $model->where([
            'usu_id' => $json['usu_id'],
            'rol_id' => $json['rol_id']
        ])->delete();

        return $this->respondDeleted([
            "message" => "Rol removido correctamente"
        ]);
    }
}
