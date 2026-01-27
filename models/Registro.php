<?php 

namespace Model;

class Registro extends ActiveRecord{

    protected static $tabla = 'registros';
    protected static $columnasDB = ['id', 'paquete_id', 'pago_id', 'token', 'usuario_id', 'regalo_id'];

    public $id;
    public $paquete_id;
    public $pago_id;
    public $token;
    public $usuario_id;
    public $regalo_id;
    
    public function __construct($args = []) {
        $this->id = $args['id'] ?? null;
        $this->paquete_id = $args['paquete_id'] ?? null;
        $this->pago_id = $args['pago_id'] ?? null;
        $this->token = $args['token'] ?? null;
        $this->usuario_id = $args['usuario_id'] ?? null;
        $this->regalo_id = $args['regalo_id'] ?? 1;
    }

}