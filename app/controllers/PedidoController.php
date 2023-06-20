<?php
require_once './models/Pedido.php';
require_once './interfaces/IApiUsable.php';
class PedidoController extends Pedido implements IApiUsable{
    public function TraerUno($request, $response, $args){
        $codigo = $args['codigo'];
        $pedido = Pedido::obtenerPedido($codigo);
        $payload = json_encode($pedido);

        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function TraerTodos($request, $response, $args){
        $lista = Pedido::obtenerTodos();
        $payload = json_encode(array("listaPedidos" => $lista));

        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }
    
    public function CargarUno($request, $response, $args){
        $parametros = $request->getParsedBody();
        $codigo = $parametros['codigoPedido'];
        $estado = $parametros['estado'];
        $importe = $parametros['importe'];
        $idMesa = $parametros['idMesa'];
        $idProducto = $parametros['idProducto'];
        $nombreCliente = $parametros['nombreCliente'];
        $pedido = new Pedido();
        $pedido->codigoPedido = $codigo;
        $pedido->idMesa = $idMesa;
        $pedido->idProducto = $idProducto;
        $pedido->nombreCliente = $nombreCliente;
        $pedido->estado = $estado;
        $pedido->importe = $importe;
        $pedido->crearPedido();

        $payload = json_encode(array("mensaje" => "Pedido creado con exito"));

        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }
    public function BorrarUno($request, $response, $args){
        
    }

    public function ModificarUno($request, $response, $args){

    }
    
}