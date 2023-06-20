<?php
require_once './models/Producto.php';
require_once './interfaces/IApiUsable.php';
class ProductoController extends Producto implements IApiUsable{
    
    public function TraerUno($request, $response, $args){
        
        $productoNombre = $args['nombre'];
        $productoTipo = $args['tipo'];
        $prd = Producto::obtenerProducto($productoNombre, $productoTipo);
        $payload = json_encode($prd);

        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function TraerTodos($request, $response, $args){
        $lista = Producto::obtenerTodos();
        $payload = json_encode(array("listaProducto" => $lista));

        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function CargarUno($request, $response, $args){
        $parametros = $request->getParsedBody();

        $nombre = $parametros['nombre'];
        $tipo = $parametros['tipo'];
        $precio = $parametros['precio'];
        $tiempo = $parametros['tiempoPreparacion'];
        $prd = new Producto();
        $prd->nombre = $nombre;
        $prd->tipo = $tipo;
        $prd->precio = $precio;
        $prd->tiempoPreparacion = $tiempo;
        $prd->crearProducto();

        $payload = json_encode(array("mensaje" => "Producto creado con exito"));

        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }
    public function BorrarUno($request, $response, $args){
        
    }
    public function ModificarUno($request, $response, $args){

    }
}