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

        // Buscar usuario
        $user = $this->db->table("usuarios")
            ->where("usu_login", $usuario)
            ->where("usu_estado", "ACTIVO")
            ->get()->getRow();

        if (!$user || !password_verify($password, $user->usu_password_hash)) {
            return $this->failUnauthorized("Usuario o contraseña incorrectos");
        }

        $userId = $user->usu_id;

        // ===============================================
        // 1️⃣ Obtener roles del usuario
        // ===============================================
        $rolesQuery = $this->db->table("usuarios_roles ur")
            ->select("r.rol_nombre")
            ->join("roles r", "r.rol_id = ur.rol_id")
            ->where("ur.usu_id", $userId)
            ->where("ur.ur_estado", "ACTIVO")
            ->get()->getResultArray();

        $roles = array_column($rolesQuery, "rol_nombre");

        // ===============================================
        // 2️⃣ Obtener módulos del usuario
        // ===============================================
        $modulosQuery = $this->db->table("usuarios_roles ur")
            ->select("m.mo_nombre")
            ->join("roles_accesos ra", "ra.rol_id = ur.rol_id")
            ->join("modulos m", "m.mo_id = ra.mo_id")
            ->where("ur.usu_id", $userId)
            ->get()->getResultArray();

        $modulos = array_unique(array_column($modulosQuery, "mo_nombre"));

        // ===============================================
        // 3️⃣ Obtener permisos CRUD por módulo
        // ===============================================
        $permisos = [];

        foreach ($modulos as $modulo) {
            $permQuery = $this->db->table("usuarios_roles ur")
                ->select("m.mo_nombre, p.per_nombre")
                ->join("roles_accesos ra", "ra.rol_id = ur.rol_id")
                ->join("modulos m", "m.mo_id = ra.mo_id")
                ->join("roles_modulos_permisos rmp", "rmp.rol_id = ur.rol_id AND rmp.mo_id = m.mo_id")
                ->join("permisos p", "p.per_id = rmp.per_id")
                ->where("ur.usu_id", $userId)
                ->where("m.mo_nombre", $modulo)
                ->get()->getResultArray();

            foreach ($permQuery as $row) {
                $permisos[$modulo][] = $row["per_nombre"];
            }

            if (!empty($permisos[$modulo])) {
                $permisos[$modulo] = array_unique($permisos[$modulo]);
            }
        }

        // ===============================================
        // 4️⃣ Construir el payload del JWT
        // ===============================================
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

        // ===============================================
        // 5️⃣ Generar JWT usando helper
        // ===============================================
        $jwt = generateJWT($payload);

        return $this->respond([
            "status"  => 200,
            "message" => "Login exitoso",
            "token"   => $jwt,
            "usuario" => $payload
        ]);
    }
}
