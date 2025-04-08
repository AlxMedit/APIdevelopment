<?php

require '../bootstrap.php';

// use App\Controller\AuthController

use App\Controller\UsuariosController;
use App\Core\Router;
use App\Controller\AuthController;
use App\Controller\CentrosCivicosController;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use App\Controller\InstalacionesController;
use App\Controller\ActividadesController;
use App\Controller\ReservasController;
use App\Controller\InscripcionesController;
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method, Authorization');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE');
header('Allow: GET, POST, OPTIONS, PUT, DELETE');

// Sin esto no funciona la conexion con angular en los metodos delete y post
// El motivo es que en estos metodos prmero se manda el metodo options y esto generaba un error
// if ($requestMethod == "OPTIONS") {
//     die();
// }

$requestMethod = $_SERVER['REQUEST_METHOD'];
$request = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$uri = explode('/', $request);

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    // Si es una solicitud OPTIONS, respondemos sin hacer nada más
    http_response_code(200);
    exit;
}

$id = null;
if (isset($uri[3])) {
    $id = (int) $uri[3];
}

function estaAutentificado()
{
    if (!isset($_SERVER['HTTP_AUTHORIZATION'])) {
        return false;
    }
    $authHeader = $_SERVER['HTTP_AUTHORIZATION'];
    $arr = explode(" ", $authHeader);
    $jwt = $arr[1];
    if (!$jwt) {
        return false;
    }
    try {
        $decoded = JWT::decode($jwt, new KEY(KEY, 'HS256'));
        return true;
    } catch (Exception $e) {
        return false;
    }
}


$router = new Router();

// Usuarios - Público (REGISTRO Y LOGIN)
$router->add(array(
    'name' => 'Registro',
    'path' => '/^\/api\/register?$/',
    'action' => UsuariosController::class,
));
$router->add(array(
    'name' => 'Login',
    'path' => '/^\/api\/login?$/',
    'action' => AuthController::class,
));

// Usuario - Privado (REFRESH TOKEN, GET, PUT, DELETE)
$router->add(array(
    'name' => 'Cambiar cosas del usuario o borrarlo y eso',
    'path' => '/^\/api\/user$/',
    'action' => UsuariosController::class,
    'perfil' => ['usuario'],
));

$router->add(array(
    'name' => 'Refresh token',
    'path' => '/^\/api\/token\/refresh?$/',
    'action' => AuthController::class,
    'perfil' => ['usuario'],
));

// Centros cívicos - Público (GET BY ID && GET ALL)
$router->add(array(
    'name' => 'Centros',
    'path' => '/^\/api\/centros(\/[0-9]+)?$/',
    'action' => CentrosCivicosController::class,
));

// Instalaciones - Público (GET BY CENTRO && GET ALL)
$router->add(array(
    'name' => 'Instalaciones de un centro',
    'path' => '/^\/api\/centros\/(\d+)\/instalaciones$/',
    'action' => InstalacionesController::class,
));

$router->add(array(
    'name' => 'Todas las instalaciones',
    'path' => '/^\/api\/instalaciones$/',
    'action' => InstalacionesController::class,
));

// Actividades - Público (GET BY CENTRO && GET ALL)
$router->add(array(
    'name' => 'Actividades de un centro',
    'path' => '/^\/api\/centros\/(\d+)\/actividades$/',
    'action' => ActividadesController::class,
));

$router->add(array(
    'name' => 'Todas las actividades',
    'path' => '/^\/api\/actividades$/',
    'action' => ActividadesController::class,
));

// Reservas - Privado (POST - Crear reserva / DELETE - Eliminar reserva / GET - Ver mis reservas)
$router->add(array( 
    'name' => 'Gestión de las reservas',
    'path' => '/^\/api\/reservas(\/[0-9]+)?$/',
    'action' => ReservasController::class,
    'perfil' => ['usuario'],
));

// Inscripciones - Privado (POST - Inscribirse a una actividad / DELETE - Desinscribirse de una actividad)
$router->add(array(
    'name' => 'Inscripciones a actividades',
    'path' => '/^\/api\/inscripciones(\/[0-9]+)?$/',
    'action' => InscripcionesController::class,
    'perfil' => ['usuario'],
));

$route = $router->match($request);
if ($route) {
    if (isset($route['perfil'])) {
        if ($route['perfil'][0] == 'usuario' && !estaAutentificado()) {
            header('HTTP/1.1 401 Unauthorized');
            $response['body'] = json_encode(['mensaje' => 'No autorizado']);
            echo $response['body'];
            exit;
        }
    }
    $controllerName = $route['action'];
    $controller = new $controllerName($requestMethod, $id);
    $controller->processRequest();
} else {
    header('HTTP/1.1 404 Not Found');
    $response['body'] = json_encode(['mensaje' => 'La ruta no existe']);
    echo $response['body'];
}