<?php
require_once './models/Mesa.php';
require_once './interfaces/IApiUsable.php';
class MesaController extends Mesa implements IApiUsable{
    public function TraerUno($request, $response, $args){
        $codigo = $args['codigo'];
        $mesa = Mesa::obtenerMesa($codigo);
        $payload = json_encode($mesa);

        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function TraerTodos($request, $response, $args){
        $lista = Mesa::obtenerTodos();
        $payload = json_encode(array("listaMesas" => $lista));

        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }
    
    public function CargarUno($request, $response, $args){
        $parametros = $request->getParsedBody();
        $codigo = $parametros['codigo'];
        $estado = $parametros['estado'];
        $mesa = new Mesa();
        $mesa->codigo = $codigo;
        $mesa->estado = $estado;
        $mesa->crearMesa();

        $payload = json_encode(array("mensaje" => "Mesa creada con exito"));

        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }
    public function BorrarUno($request, $response, $args){
        
    }
    public function ModificarUno($request, $response, $args){

    }
}