<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');
$routes->get('prueba', 'Prueba::index');

// Rutas API

// Rutas de seguridad
$routes->group('seguridad', function($routes) {

    // === LOGIN / REGISTRO ===
    $routes->post('login', 'AuthController::login');
    $routes->post('register', 'AuthController::create');

    // === ROLES ===
    $routes->get('roles', 'SeguridadController::listarRoles');

    // === MODULOS DEL ROL ===
    $routes->get('roles/(:num)/accesos', 'SeguridadController::listarAccesosRol/$1');
    $routes->post('roles/(:num)/accesos', 'SeguridadController::asignarModuloRol/$1');
    $routes->delete('accesos/(:num)', 'SeguridadController::quitarModulo/$1');

    // === PERMISOS POR ACCESO ===
    $routes->get('accesos/(:num)/permisos', 'SeguridadController::permisosPorAcceso/$1');
    $routes->post('accesos/(:num)/permisos', 'SeguridadController::asignarPermisoAcceso/$1');
    $routes->delete('accesos/permisos/(:num)', 'SeguridadController::quitarPermiso/$1');

    // === USUARIOS-ROLES ===
    $routes->get('usuarios/(:num)/roles', 'SeguridadController::rolesPorUsuario/$1');
    $routes->post('usuarios/(:num)/roles', 'SeguridadController::asignarRolesUsuario/$1');
    $routes->post('usuarios/(:num)/roles/quitar', 'SeguridadController::quitarRolUsuario/$1');

    // === PERMISOS FINALES ===
    $routes->get('usuarios/(:num)/permisos', 'SeguridadController::permisosPorUsuario/$1');

    $routes->get('usuarios', 'SeguridadController::listarUsuarios');


});



// Rutas protegidas por JWT
$routes->group('pacientes', ['filter' => 'jwt'], function($routes){
    $routes->get('/', 'PacientesController::index');       // Listar pacientes
    $routes->post('/', 'PacientesController::create');     // Crear paciente
    $routes->put('(:num)', 'PacientesController::update/$1'); // Actualizar paciente
    $routes->delete('(:num)', 'PacientesController::delete/$1'); // Eliminar paciente
    $routes->get('buscar/documento', 'PacientesController::buscarPorDocumento');
    $routes->get('buscar/apellidos', 'PacientesController::buscarPorApellidos');

});

// Rutas de médicos
$routes->group('medicos', ['filter' => 'jwt'], function($routes){

    $routes->get('/', 'MedicosController::index');
    $routes->post('/', 'MedicosController::create');
    $routes->get('buscar-dni', 'MedicosController::buscarPorDni');
    $routes->get('buscar-apellidos', 'MedicosController::buscarPorApellidos');
    $routes->get('(:num)', 'MedicosController::show/$1');
    $routes->put('(:num)', 'MedicosController::update/$1');
    $routes->delete('(:num)', 'MedicosController::delete/$1');

});

// Rutas de consultas
$routes->group('consultas', ['filter' => 'jwt'], function($routes){
    $routes->get('/', 'ConsultasController::index');       // Listar consultas por paciente
    $routes->get('(:num)', 'ConsultasController::show/$1'); // Obtener consulta por ID
    $routes->post('/', 'ConsultasController::create');     // Registrar consulta
});

// Rutas de historias clínicas
$routes->group('historias-clinicas', ['filter' => 'jwt'], function($routes){

    // Obtener historia por ID de paciente
    $routes->get('paciente/(:num)', 'HistoriaClinicaController::show/$1');

    // Crear historia clínica
    $routes->post('/', 'HistoriaClinicaController::create');

    // Actualizar historia clínica (his_id)
    $routes->put('(:num)', 'HistoriaClinicaController::update/$1');

    // Listar versiones de una historia clínica
    $routes->get('(:num)/versiones', 'HistoriaClinicaController::versiones/$1');

    // Obtener una versión específica
    $routes->get('version/(:num)', 'HistoriaClinicaController::version/$1');
});



// Rutas de triaje
$routes->group('triaje', ['filter' => 'jwt'], function($routes){
    $routes->post('/', 'TriajeController::create'); // Registrar triaje
    $routes->get('(:num)', 'TriajeController::show/$1'); // Obtener triaje por consulta
    $routes->get('clasificaciones', 'TriajeController::clasificaciones'); // Listar clasificaciones de triaje
});

$routes->options('(:any)', function () {
    return service('response')->setStatusCode(200);
});

