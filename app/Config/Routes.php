<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');
$routes->get('prueba', 'Prueba::index');

// Rutas API
$routes->group('seguridad', params: function($routes) {
    // Ruta para login
    $routes->post('login', 'AuthController::login'); // Generar JWT

    // Ruta para crear un nuevo usuario
    $routes->post('register', 'AuthController::create'); // Crear usuario
});

// Rutas protegidas por JWT
$routes->group('pacientes', ['filter' => 'jwt'], function($routes){
    $routes->get('/', 'PacientesController::index');       // Listar pacientes
    $routes->post('/', 'PacientesController::create');     // Crear paciente
    $routes->put('(:num)', 'PacientesController::update/$1'); // Actualizar paciente
    $routes->delete('(:num)', 'PacientesController::delete/$1'); // Eliminar paciente
});

// Rutas de médicos
$routes->group('medicos', ['filter' => 'jwt'], function($routes){
    $routes->get('/', 'MedicosController::index');         // Listar médicos
    $routes->post('/', 'MedicosController::create');       // Crear médico
    $routes->get('(:num)', 'MedicosController::show/$1');   // Obtener médico por ID
    $routes->put('(:num)', 'MedicosController::update/$1'); // Actualizar médico
    $routes->delete('(:num)', 'MedicosController::delete/$1'); // Eliminar médico
});

// Rutas de consultas
$routes->group('consultas', ['filter' => 'jwt'], function($routes){
    $routes->get('/', 'ConsultasController::index');       // Listar consultas por paciente
    $routes->get('(:num)', 'ConsultasController::show/$1'); // Obtener consulta por ID
    $routes->post('/', 'ConsultasController::create');     // Registrar consulta
});

// Rutas de historia clínica
$routes->group('historias-clinicas', ['filter' => 'jwt'], function($routes){
    $routes->get('(:num)', 'HistoriaClinicaController::show/$1'); // Obtener historia clínica por paciente
});

// Rutas de triaje
$routes->group('triaje', ['filter' => 'jwt'], function($routes){
    $routes->post('/', 'TriajeController::create'); // Registrar triaje
    $routes->get('(:num)', 'TriajeController::show/$1'); // Obtener triaje por consulta
    $routes->get('clasificaciones', 'TriajeController::clasificaciones'); // Listar clasificaciones de triaje
});
