<?php
    require_once './models/Usuario.php';
    class AutenticadorUsuario{

        public static function VerificarUsuario($request, $handler){
            $parametros = $request->getParsedBody();
            $rol = $parametros['rol'];
            if(Usuario::ValidarRolUsuario($rol)){
                return $handler->handle($request);
            }
            else{
                throw new Exception('Rol invalido');
            }
        }

        public static function esSocio($request, $handler){
            $cookies = $request->getCookieParams();
            $token = $cookies['jwt'];
            AutentificadorJWT::VerificarToken($token);
            $datos = AutentificadorJWT::ObtenerData($token);
            if(Usuario::esSocio($datos->rol)){
                return $handler->handle($request);
            }
            throw new Exception('NO SOS SOCIO');
        }

        public static function ValidarCampos($request, $handler){
            $parametros = $request->getParsedBody();
            $nombre = $parametros['nombre'];
            $email = $parametros['email'];
            $clave = $parametros['clave'];
            $rol = $parametros['rol'];
            $estado = $parametros['estado'];
            if(Usuario::ValidarCamposUsuario($nombre, $email, $clave, $rol, $estado)){
                return $handler->handle($request);
            }
            throw new Exception('Campos Invalidos');
        }
    }
?>