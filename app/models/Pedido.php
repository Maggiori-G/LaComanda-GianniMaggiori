<?php
class Pedido{
    public $id;
    public $codigoPedido;
    public $idMesa;
    public $idProducto;
    public $nombreCliente;
    public $estado;
    public $importe;
    
    /*
        repensar la relacion producto/pedido
        manejar los tiempos de pedidos
        cuidado con los estados
    */
    public function crearPedido()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("INSERT INTO pedidos (codigoPedido, idMesa, idProducto, nombreCliente, estado, importe) VALUES (:codigoPedido, :idMesa, :idProducto, :nombreCliente, :estado, :importe)");
        $consulta->bindValue(':codigoPedido', $this->codigoPedido, PDO::PARAM_STR);
        $consulta->bindValue(':idMesa', $this->codigoPedido, PDO::PARAM_INT);
        $consulta->bindValue(':idProducto', $this->codigoPedido, PDO::PARAM_INT);
        $consulta->bindValue(':nombreCliente', $this->nombreCliente, PDO::PARAM_INT);
        $consulta->bindValue(':estado', $this->estado, PDO::PARAM_STR);
        $consulta->bindValue(':importe', $this->importe, PDO::PARAM_INT);
        

        $consulta->execute();

        return $objAccesoDatos->obtenerUltimoId();
    }

    public static function obtenerTodos()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT id, codigo,idMesa, idProducto, estado, importe FROM pedidos");
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_CLASS, 'Pedido');
    }

    public static function obtenerPedido($codigo)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT id, codigo,idMesa, idProducto, estado, importe FROM pedidos WHERE codigo = :codigo");
        $consulta->bindValue(':codigo', $codigo, PDO::PARAM_STR);
        $consulta->execute();

        return $consulta->fetchObject('Pedido');
    }
}