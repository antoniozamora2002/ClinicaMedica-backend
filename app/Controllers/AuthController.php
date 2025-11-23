<?php

namespace App\Controllers;

use CodeIgniter\RESTful\ResourceController;

class AuthController extends ResourceController
{
    protected $format = 'json';
    protected $db;

    public function __construct()
    {
        $this->db = \Config\Database::connect('seguridad');
    }

    /**
     * Crear un nuevo usuario
     */
    public function create()
    {
        $data = $this->request->getJSON();

        if (
            empty($data->usu_login) ||
            empty($data->usu_password) ||
            empty($data->usu_nombre_completo) ||
            empty($data->usu_correo)
        ) {
            return $this->respond(["status" => 400, "message" => "Faltan datos"], 400);
        }

        $hashedPassword = password_hash($data->usu_password, PASSWORD_DEFAULT);

        $this->db->table("usuarios")->insert([
            "usu_login"          => $data->usu_login,
            "usu_password_hash"  => $hashedPassword,
            "usu_nombre_completo"=> $data->usu_nombre_completo,
            "usu_correo"         => $data->usu_correo,
            "usu_estado"         => "ACTIVO",
            "usu_creado_en"      => date("Y-m-d H:i:s")
        ]);

        if ($this->db->affectedRows() > 0) {
            return $this->respond([
                'status'  => 201,
                'message' => 'Usuario creado correctamente'
            ]);
        }

        return $this->failServerError("Error al crear usuario");
    }

    /**
     * LOGIN — GENERA TOKEN JWT COMPLETO
     */
    public function login()
    {
        $usuario = $this->request->getVar("usuario");
        $password = $this->request->getVar("password");

        if (!$usuario || !$password) {
            return $this->respond(["status" => 400, "message" => "Faltan credenciales"], 400);
        }

        // === 1. Validar usuario ===
        $user = $this->db->table("usuarios")
            ->where("usu_login", $usuario)
            ->where("usu_estado", "ACTIVO")
            ->get()->getRow();

        if (!$user || !password_verify($password, $user->usu_password_hash)) {
            return $this->failUnauthorized("Usuario o contraseña incorrectos");
        }

        $userId = $user->usu_id;

        // ============================================================
        // 2. ROLES DEL USUARIO
        // ============================================================
        $rolesQuery = $this->db->table("usuarios_roles ur")
            ->select("r.rol_nombre, r.rol_id")
            ->join("roles r", "r.rol_id = ur.rol_id")
            ->where("ur.usu_id", $userId)
            ->where("ur.ur_estado", "ACTIVO")
            ->get()->getResultArray();

        $roles = array_column($rolesQuery, "rol_nombre");
        $rolesIds = array_column($rolesQuery, "rol_id");

        // ============================================================
        // 3. MODULOS PERMITIDOS (roles_accesos)
        // ============================================================
        $modulosQuery = $this->db->table("roles_accesos ra")
            ->select("m.mo_id, m.mo_nombre")
            ->join("modulos m", "m.mo_id = ra.mo_id")
            ->whereIn("ra.rol_id", $rolesIds)
            ->where("ra.ra_estado", "ACTIVO")
            ->get()->getResultArray();

        $modulos = array_unique(array_column($modulosQuery, "mo_nombre"));
        $modulosIds = array_unique(array_column($modulosQuery, "mo_id"));

        // ============================================================
        // 4. PERMISOS CRUD POR MÓDULO
        // ============================================================
        $permisos = [];

        foreach ($modulosQuery as $mod) {

            $permQuery = $this->db->table("roles_accesos ra")
                ->select("m.mo_nombre, p.per_nombre")
                ->join("modulos m", "m.mo_id = ra.mo_id")
                ->join("roles_modulos_permisos rmp", "rmp.ra_id = ra.ra_id")
                ->join("permisos p", "p.per_id = rmp.per_id")
                ->where("ra.mo_id", $mod["mo_id"])
                ->whereIn("ra.rol_id", $rolesIds)
                ->where("ra.ra_estado", "ACTIVO")
                ->where("rmp.rmp_estado", "ACTIVO")
                ->get()->getResultArray();

            foreach ($permQuery as $row) {
                $permisos[$mod["mo_nombre"]][] = $row["per_nombre"];
            }

            if (isset($permisos[$mod["mo_nombre"]])) {
                $permisos[$mod["mo_nombre"]] = array_unique($permisos[$mod["mo_nombre"]]);
            }
        }

        // ============================================================
        // 5. GENERAR JWT
        // ============================================================
        $payload = [
            "userId"   => $userId,
            "nombre"   => $user->usu_nombre_completo,
            "usuario"  => $user->usu_login,
            "roles"    => $roles,
            "modulos"  => $modulos,
            "permisos" => $permisos,
            "iat"      => time(),
            "exp"      => time() + 3600
        ];

        $jwt = generateJWT($payload);

        return $this->respond([
            "status"  => 200,
            "message" => "Login exitoso",
            "token"   => $jwt,
            "usuario" => $payload
        ]);
    }

}
