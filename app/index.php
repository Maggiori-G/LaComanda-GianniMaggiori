<?php
// php -S localhost:666 -t app
error_reporting(-1);
ini_set('display_errors', 1);

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\Factory\AppFactory;
use Slim\Routing\RouteCollectorProxy;
use Slim\Routing\RouteContext;

require __DIR__ . '/../vendor/autoload.php';

require_once './db/AccesoDatos.php';
require_once './middlewares/AutentificadorJWT.php';
require_once './middlewares/AutenticadorUsuarios.php';

require_once './controllers/UsuarioController.php';
require_once './controllers/ProductoController.php';
require_once './controllers/MesaController.php';
require_once './controllers/PedidoController.php';

// Load ENV
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->safeLoad();

// Instantiate App
$app = AppFactory::create();

// Add error middleware
$errorMiddleware = function ($request, $exception, $displayErrorDetails) use ($app) {
    $statusCode = 500;
    $errorMessage = $exception->getMessage();  
    $response = $app->getResponseFactory()->createResponse($statusCode);
    $response->getBody()->write(json_encode(['error' => $errorMessage]));
    return $response->withHeader('Content-Type', 'application/json');
};
  
$app->addErrorMiddleware(true, true, true)->setDefaultErrorHandler($errorMiddleware);

$app->get('/admin', function (Request $request, Response $response){
    $usuario = new Usuario();
    $usuario->nombre = 'admin';
    $usuario->email = 'ejemplo@gmail.com';
    $usuario->clave = '1234';
    $usuario->rol = 'socio';
    $usuario->estado = 'activo';
    $usuario->crearUsuario();
    $response->getBody()->write('Creado super usario');
    return $response->withHeader('Content-Type', 'application/json');
    
});
// Routes
$app->group('/usuarios', function (RouteCollectorProxy $group) {
    $group->get('[/]', \UsuarioController::class . ':TraerTodos');
    $group->get('/{id}', \UsuarioController::class . ':TraerUno')
    ->add(\AutenticadorUsuario::class.':esSocio');
    $group->post('[/]', \UsuarioController::class . ':CargarUno')
    ->add(\AutenticadorUsuario::class.':ValidarCampos')
    ->add(\AutenticadorUsuario::class.':VerificarUsuario')
    ->add(\AutenticadorUsuario::class.':esSocio');
    $group->put('/{id}', \UsuarioController::class . ':ModificarUno')
    ->add(\AutenticadorUsuario::class.':ValidarCampos')
    ->add(\AutenticadorUsuario::class.':esSocio');
    $group->delete('/{id}', \UsuarioController::class . ':BorrarUno')
    ->add(\AutenticadorUsuario::class.':esSocio');
});

$app->group('/productos', function (RouteCollectorProxy $group) {
    $group->get('[/]', \ProductoController::class.':TraerTodos');
    $group->get('/{producto}', \ProductoController::class.':TraerUno');
    $group->post('[/]', \ProductoController::class.':CargarUno');
});

$app->group('/mesas', function (RouteCollectorProxy $group) {
    $group->get('[/]', \MesaController::class.':TraerTodos');
    $group->get('/{mesa}', \MesaController::class.':TraerUno');
    $group->post('[/]', \MesaController::class.':CargarUno');
});

$app->group('/pedido', function (RouteCollectorProxy $group) {
    $group->get('[/]', \PedidoController::class.':TraerTodos');
    $group->get('/{pedido}', \PedidoController::class.':TraerUno');
    $group->post('[/]', \PedidoController::class.':CargarUno');
});

$app->group('/login', function (RouteCollectorProxy $group) {
    $group->post('[/]', \UsuarioController::class.'::Loguear');
});
// JWT test routes
$app->group('/jwt', function (RouteCollectorProxy $group) {

    $group->post('/crearToken', function (Request $request, Response $response) {
        $parametros = $request->getParsedBody();

        $usuario = $parametros['usuario'];
        $perfil = $parametros['perfil'];
        $alias = $parametros['alias'];

        $datos = array('usuario' => $usuario, 'perfil' => $perfil, 'alias' => $alias);

        $token = AutentificadorJWT::CrearToken($datos);
        $payload = json_encode(array('jwt' => $token));

        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    });

    $group->get('/devolverPayLoad', function (Request $request, Response $response) {
        $header = $request->getHeaderLine('Authorization');
        $token = trim(explode("Bearer", $header)[1]);

        try {
            $payload = json_encode(array('payload' => AutentificadorJWT::ObtenerPayLoad($token)));
        } 
        catch (Exception $e) {
            $payload = json_encode(array('error' => $e->getMessage()));
        }

        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    });

    $group->get('/devolverDatos', function (Request $request, Response $response) {
        $header = $request->getHeaderLine('Authorization');
        $token = trim(explode("Bearer", $header)[1]);

        try {
            $payload = json_encode(array('datos' => AutentificadorJWT::ObtenerData($token)));
        } catch (Exception $e) {
            $payload = json_encode(array('error' => $e->getMessage()));
        }

        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    });

    $group->get('/verificarToken', function (Request $request, Response $response) {
        $header = $request->getHeaderLine('Authorization');
        $token = trim(explode("Bearer", $header)[1]);
        $esValido = false;

        try {
        AutentificadorJWT::verificarToken($token);
            $esValido = true;
        } catch (Exception $e) {
            $payload = json_encode(array('error' => $e->getMessage()));
        }

        if ($esValido) {
            $payload = json_encode(array('valid' => $esValido));
        }

        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    });
});


$app->get('[/]', function (Request $request, Response $response) {
    $payload = json_encode(array("mensaje" => "Prueba de conex"));
    $response->getBody()->write($payload);
    return $response->withHeader('Content-Type', 'application/json');
});

$app->run();
