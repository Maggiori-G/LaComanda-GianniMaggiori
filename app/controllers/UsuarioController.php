<?php
require_once './models/Usuario.php';
require_once './interfaces/IApiUsable.php';

class UsuarioController extends Usuario implements IApiUsable
{
    public function CargarUno($request, $response, $args)
    {
        $parametros = $request->getParsedBody();

        $nombre = $parametros['nombre'];
        $email = $parametros['email'];
        $clave = $parametros['clave'];
        $rol = $parametros['rol'];
        $estado = $parametros['estado'];
        $usr = new Usuario();
        $usr->nombre = $nombre;
        $usr->email = $email;
        $usr->clave = $clave;
        $usr->rol = $rol;
        $usr->estado = $estado;
        $usr->crearUsuario();

        $payload = json_encode(array("mensaje" => "Usuario creado con exito"));

        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function TraerUno($request, $response, $args)
    {
        $usr = $args['nombre'];
        $usuario = Usuario::obtenerUsuario($usr);
        $payload = json_encode($usuario);

        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function TraerTodos($request, $response, $args)
    {
        $lista = Usuario::obtenerTodos();
        $payload = json_encode(array("listaUsuario" => $lista));

        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }
    
    public function ModificarUno($request, $response, $args)
    {
        $parametros = $request->getParsedBody();
        $usuario = Usuario::obtenerUsuario($parametros['id']);
        Usuario::modificarUsuario($usuario);
        $payload = json_encode(array("mensaje" => "Usuario modificado con exito"));
        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function BorrarUno($request, $response, $args)
    {
        
        $usuario = Usuario::obtenerUsuario($args['id']);
        Usuario::borrarUsuario($usuario);

        $payload = json_encode(array("mensaje" => "Usuario borrado con exito"));

        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }

    public static function Loguear($request, $response, $args){
        $parametros = $request->getParsedBody();
        $nombre = $parametros['nombre'];
        $clave = $parametros['clave'];
        $usuario = Usuario::obtenerUsuarioNombre($nombre);
        if($usuario !== null && password_verify($clave, $usuario->clave)){
            $token = AutentificadorJWT::CrearToken(array('id' => $usuario->id, 'rol' => $usuario->rol));
            setcookie('jwt', $token, time()+60*60*24*30, '/', 'localhost', false, true);
            $payload = json_encode(array('mensaje'=>'Logueo Exitoso'));
        }
        else{
            $payload = json_encode(array('mensaje'=>'Datos Invalidos'));
            
        }
        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }

    
}
