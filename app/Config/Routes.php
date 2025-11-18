<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
//$routes->get('/', 'Home::index');
$routes->get('/', 'home::index');
$routes->get('/prueba', 'prueba::index');

// Rutas API
$routes->group('seguridad', function($routes) {
    // Ruta para login
    $routes->post('login', 'AuthController::login'); // Generar JWT

    // Ruta para crear un nuevo usuario
    $routes->post('register', 'AuthController::create'); // Crear usuario
});

$routes->group('pacientes', ['filter' => 'jwt'], function($routes){
    $routes->get('/', 'PacientesController::index');
    $routes->post('/', 'PacientesController::create');
    $routes->put('(:num)', 'PacientesController::update/$1');
    $routes->delete('(:num)', 'PacientesController::delete/$1');
});


