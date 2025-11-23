<?php

namespace App\Controllers;

use CodeIgniter\RESTful\ResourceController;
use App\Models\TriajeModel;
use App\Models\ClasificacionTriajeModel;

class TriajeController extends ResourceController
{
    protected $format = 'json';

    // ============================================================
    // OBTENER TRIAJE POR CONSULTA
    // ============================================================
    public function show($conId = null)
    {
        if (!$conId) {
            return $this->failNotFound("ID de consulta es necesario.");
        }

        $model = new TriajeModel();
        $data = $model->getTriajeByConsulta($conId);

        if (!$data) {
            return $this->failNotFound("No se encontró el triaje para esta consulta.");
        }

        return $this->respond($data);
    }

    // ============================================================
    // REGISTRAR TRIAJE
    // ============================================================
    public function create()
    {
        $json = $this->request->getJSON(true);

        // Validación de campos
        if (empty($json['con_id']) || empty($json['usu_id']) || empty($json['cla_id'])) {
            return $this->respond([
                'status' => 400,
                'message' => 'Faltan datos importantes (con_id, usu_id, cla_id)'
            ], 400);
        }

        // Insertar triaje
        $model = new TriajeModel();
        $model->insert([
            'con_id'                => $json['con_id'],
            'usu_id'                => $json['usu_id'],
            'cla_id'                => $json['cla_id'],
            'tri_peso_kg'           => $json['tri_peso_kg'],
            'tri_talla_cm'          => $json['tri_talla_cm'],
            'tri_presion_arterial'  => $json['tri_presion_arterial'],
            'tri_temperatura'       => $json['tri_temperatura'],
            'tri_frecuencia_cardiaca'=> $json['tri_frecuencia_cardiaca'],
            'tri_saturacion_o2'     => $json['tri_saturacion_o2'],
            'tri_imc'               => $json['tri_imc'],
            'tri_fecha_hora'        => date('Y-m-d H:i:s')
        ]);

        return $this->respondCreated([
            'status'  => 201,
            'message' => 'Triaje registrado exitosamente'
        ]);
    }

    // ============================================================
    // LISTAR CLASIFICACIONES DE TRIAJE
    // ============================================================
    public function clasificaciones()
    {
        $model = new ClasificacionTriajeModel();
        $clasificaciones = $model->getClasificaciones();

        return $this->respond($clasificaciones);
    }
}
