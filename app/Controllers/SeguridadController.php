<?php

namespace App\Controllers;

use CodeIgniter\RESTful\ResourceController;
use App\Models\RolesModulosPermisosModel;
use App\Models\UsuariosRolesModel;

class SeguridadController extends ResourceController
{
    protected $format = 'json';

    public function permisosPorRol($rol_id)
    {
        $model = new RolesModulosPermisosModel();

        $data = $model->where('rol_id', $rol_id)->findAll();

        return $this->respond($data);
    }

    public function listarRoles()
    {
        $rolesModel = new \App\Models\RolesModel();

        $roles = $rolesModel->where('rol_estado', 'ACTIVO')->findAll();

        return $this->respond($roles);
    }

    public function rolesPorUsuario($usu_id = null)
    {
        if ($usu_id === null)
            return $this->failValidationError("Debes enviar el ID del usuario.");

        $usuariosRoles = new \App\Models\UsuariosRolesModel();
        $rolesModel = new \App\Models\RolesModel();

        // Obtener los IDs de los roles asignados
        $asignados = $usuariosRoles
            ->where('usu_id', $usu_id)
            ->where('ur_estado', 'ACTIVO')
            ->findAll();

        if (!$asignados)
            return $this->respond([
                "message" => "El usuario no tiene roles asignados",
                "roles" => []
            ]);

        // Extraer solo los IDs
        $rolesIds = array_column($asignados, 'rol_id');

        // Obtener detalles de cada rol
        $roles = $rolesModel
            ->whereIn('rol_id', $rolesIds)
            ->findAll();

        return $this->respond($roles);
    }

    public function asignarRolesUsuario($usuarioId = null)
{
    $json = $this->request->getJSON(true);

    if (!$json || !isset($json['rol_id']))
        return $this->failValidationError("Debes enviar rol_id.");

    $rolId = $json['rol_id'];

    $usuarioRolModel = new UsuariosRolesModel();

    // ¿Ya está asignado?
    $existe = $usuarioRolModel
        ->where('usu_id', $usuarioId)
        ->where('rol_id', $rolId)
        ->where('ur_estado', 'ACTIVO')
        ->first();

    if ($existe) {
        return $this->failResourceExists("El usuario ya tiene este rol.");
    }

    // Asignar rol
    $usuarioRolModel->insert([
        'usu_id' => $usuarioId,
        'rol_id' => $rolId,
        'ur_estado' => 'ACTIVO'
    ]);

    return $this->respondCreated([
        "message" => "Rol asignado correctamente",
        "usuario" => $usuarioId,
        "rol_asignado" => $rolId
    ]);
}



}
