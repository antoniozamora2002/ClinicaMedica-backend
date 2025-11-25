<?php

namespace App\Controllers;

use CodeIgniter\RESTful\ResourceController;
use App\Models\RolesModel;
use App\Models\ModulosModel;
use App\Models\PermisosModel;
use App\Models\RolesAccesosModel;
use App\Models\RolesModulosPermisosModel;
use App\Models\UsuariosRolesModel;

class SeguridadController extends ResourceController
{
    protected $format = 'json';

    // ======================================
    // LISTAR ROLES
    // ======================================
    public function listarRoles()
    {
        $model = new RolesModel();
        return $this->respond(
            $model->where('rol_estado', 'ACTIVO')->findAll()
        );
    }

    // ======================================
    // LISTAR MÓDULOS
    // ======================================
    public function listarModulos()
    {
        $model = new ModulosModel();
        return $this->respond($model->where('mo_estado', 'ACTIVO')->findAll());
    }

    // ======================================
    // LISTAR PERMISOS (READ, CREATE, UPDATE, DELETE)
    // ======================================
    public function listarPermisos()
    {
        $model = new PermisosModel();
        return $this->respond($model->findAll());
    }

    // ======================================
    // LISTAR ACCESOS (ROL + MÓDULOS)
    // ======================================
    public function listarAccesosRol($rol_id)
    {
        $model = new RolesAccesosModel();

        return $this->respond(
            $model->where('rol_id', $rol_id)
                  ->where('ra_estado', 'ACTIVO')
                  ->findAll()
        );
    }

    // ======================================
    // LISTAR PERMISOS (POR RA_ID)
    // ======================================
    public function permisosPorAcceso($ra_id)
    {
        $model = new RolesModulosPermisosModel();

        return $this->respond(
            $model->where('ra_id', $ra_id)
                  ->where('rmp_estado', 'ACTIVO')
                  ->findAll()
        );
    }

    // ======================================
    // ASIGNAR PERMISO A UN ACCESO (ra_id + per_id)
    // ======================================
    public function asignarPermisosRol($ra_id)
    {
        $json = $this->request->getJSON(true);

        if (!isset($json['per_id']))
            return $this->failValidationError("Debe enviar per_id.");

        $per_id = $json['per_id'];
        $model = new RolesModulosPermisosModel();

        // Evitar duplicados
        $existe = $model
            ->where('ra_id', $ra_id)
            ->where('per_id', $per_id)
            ->where('rmp_estado', 'ACTIVO')
            ->first();

        if ($existe)
            return $this->failResourceExists("Ese permiso ya está asignado para este acceso.");

        // Insertar
        $model->insert([
            'ra_id' => $ra_id,
            'per_id' => $per_id,
            'rmp_estado' => 'ACTIVO'
        ]);

        return $this->respondCreated([
            "message" => "Permiso asignado correctamente."
        ]);
    }

    // ======================================
    // ROLES POR USUARIO
    // ======================================
    public function rolesPorUsuario($usu_id)
    {
        $model = new UsuariosRolesModel();
        $rolesModel = new RolesModel();

        $asignados = $model
            ->where('usu_id', $usu_id)
            ->where('ur_estado', 'ACTIVO')
            ->findAll();

        if (!$asignados)
            return $this->respond([
                "message" => "El usuario no tiene roles asignados",
                "roles" => []
            ]);

        $rolesIds = array_column($asignados, 'rol_id');

        return $this->respond(
            $rolesModel->whereIn('rol_id', $rolesIds)->findAll()
        );
    }

    // ======================================
    // ASIGNAR ROL A USUARIO
    // ======================================
    public function asignarRolesUsuario($usu_id)
    {
        $json = $this->request->getJSON(true);

        if (!isset($json['rol_id']))
            return $this->failValidationError("Debe enviar rol_id.");

        $rol_id = $json['rol_id'];
        $model = new UsuariosRolesModel();

        $existe = $model
            ->where('usu_id', $usu_id)
            ->where('rol_id', $rol_id)
            ->where('ur_estado', 'ACTIVO')
            ->first();

        if ($existe)
            return $this->failResourceExists("El usuario ya tiene este rol.");

        $model->insert([
            'usu_id' => $usu_id,
            'rol_id' => $rol_id,
            'ur_estado' => 'ACTIVO'
        ]);

        return $this->respondCreated([
            "message" => "Rol asignado correctamente.",
            "usuario" => $usu_id,
            "rol_asignado" => $rol_id
        ]);
    }

    // ======================================
    // PERMISOS FINALES POR USUARIO
    // ======================================
    public function permisosPorUsuario($usu_id)
    {
        $rolesModel = new UsuariosRolesModel();
        $rmp = new RolesModulosPermisosModel();
        $ra = new RolesAccesosModel();

        // Roles asignados
        $roles = $rolesModel
            ->where('usu_id', $usu_id)
            ->where('ur_estado', 'ACTIVO')
            ->findAll();

        if (!$roles)
            return $this->respond([
                "message" => "El usuario no tiene roles asignados.",
                "permisos" => []
            ]);

        // Extraer IDs de roles
        $rolesIds = array_column($roles, 'rol_id');

        // Obtener accesos (ra_id) de esos roles
        $accesos = $ra
            ->whereIn('rol_id', $rolesIds)
            ->where('ra_estado', 'ACTIVO')
            ->findAll();

        $raIds = array_column($accesos, 'ra_id');

        // Obtener permisos finales del usuario
        $permisos = $rmp
            ->whereIn('ra_id', $raIds)
            ->where('rmp_estado', 'ACTIVO')
            ->findAll();

        return $this->respond([
            "usuario" => $usu_id,
            "accesos" => $accesos,
            "permisos" => $permisos
        ]);
    }

// ======================================
// QUITAR ROL A USUARIO (Soft Delete)
// ======================================
    public function quitarRolUsuario($usu_id)
    {
        $json = $this->request->getJSON(true);

        if (!isset($json['rol_id']))
            return $this->failValidationError("Debe enviar rol_id.");

        $rol_id = $json['rol_id'];
        $model = new UsuariosRolesModel();

        // Buscar asignación
        $asignado = $model
            ->where('usu_id', $usu_id)
            ->where('rol_id', $rol_id)
            ->where('ur_estado', 'ACTIVO')
            ->first();

        if (!$asignado)
            return $this->failNotFound("Este rol no está asignado al usuario.");

        // Marcar como INACTIVO
        $model->update($asignado['ur_id'], [
            'ur_estado' => 'INACTIVO'
        ]);

        return $this->respondDeleted([
            "message" => "Rol removido correctamente.",
            "usuario" => $usu_id,
            "rol_removido" => $rol_id
        ]);
    }
    public function listarUsuarios()
    {
        $db = \Config\Database::connect('seguridad');

        $usuarios = $db->table('usuarios')
            ->select('usu_id, usu_login, usu_nombre_completo, usu_correo, usu_estado, usu_creado_en')
            ->where('usu_estado', 'ACTIVO')
            ->get()
            ->getResult();

        return $this->respond([
            "status" => 200,
            "usuarios" => $usuarios
        ]);
    }


}
