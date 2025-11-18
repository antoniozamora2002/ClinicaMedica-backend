<?php

namespace App\Controllers;

class Prueba extends BaseController
{
    public function index()
    {
        // Conexion core
        $dbCore = \Config\Database::connect('default');

        // Conexion seguridad
        $dbSeg = \Config\Database::connect('seguridad');

        // Probar clinica_core
        try {
            $core = $dbCore->query("SELECT 1")->getRow();
            echo "✔ Conexión a clinica_core OK<br>";
        } catch (\Exception $e) {
            echo "❌ Error clinica_core: " . $e->getMessage() . "<br>";
        }

        // Probar clinica_seguridad
        try {
            $seg = $dbSeg->query("SELECT 1")->getRow();
            echo "✔ Conexión a clinica_seguridad OK<br>";
        } catch (\Exception $e) {
            echo "❌ Error clinica_seguridad: " . $e->getMessage() . "<br>";
        }
    }
}
