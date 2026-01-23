<?php

namespace Controllers;

use Model\Dia;
use Model\Hora;
use MVC\Router;
use Model\Evento;
use Model\Regalo;
use Model\Paquete;
use Model\Ponente;
use Model\Usuario;
use Model\Registro;
use Model\Categoria;
use Model\EventosRegistros;

class RegistroController
{

    public static function crear(Router $router){

        if (!isAuth()) {
            header('Location: /');
            return;
        }

        $registro = Registro::where('usuario_id', $_SESSION['id']);
        if(isset($registro) && ($registro->paquete_id === '3' || $registro->paquete_id === '2')){ 
            header('Location: /boleto?id=' . urlencode($registro->token));
            return;
        }

        if($registro->paquete_id === '1'){
            header('Location: /finalizar-registro/conferencias');
            return;
        }

        $token = substr(md5(uniqid(rand(), true)), 0, 8);; 

        // Render a la vista
        $router->render('registro/crear', [
            'titulo' => 'Finalizar Registro',
            'token'  => $token
        ]);
    }
    


    public static function gratis(Router $router){

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Procesar el registro gratis
            if (!isAuth()) {
                header('Location: /');
                return;
            }

            $registro = Registro::where('usuario_id', $_SESSION['id']);
            if (isset($registro) && $registro->paquete_id === 3) {
                header('Location: /boleto?id=' . urlencode($registro->token));
                return;
            }

            $token = substr(md5(uniqid(rand(), true)), 0, 8);

            $datos = [
                'paquete_id' => 3,
                'pago_id' => '',
                'token' => $token,
                'usuario_id' => $_SESSION['id']
            ];

            $registro = new Registro($datos);
            $resultado = $registro->guardar();

            if ($resultado) {
                header('Location: /boleto?id' . urlencode($registro->token));
            }
        }
    }

    public static function crearOrdenPaypal(){
        header('Content-Type: application/json; charset=utf-8');
        session_start();

        $input = json_decode(file_get_contents('php://input'), true) ?? [];
        $paqueteId = (int)($input['paquete_id'] ?? 0);

        if (!in_array($paqueteId, [1, 2], true)) {
            http_response_code(400);
            echo json_encode(['error' => 'Paquete inválido']);
            exit;
        }

        $precio = $paqueteId === 1 ? 199.00 : 49.00;
        $detalle = $paqueteId === 1
            ? 'Pase Presencial DevWebCamp'
            : 'Pase Virtual DevWebCamp';

        $paypalToken = paypal_access_token();

        $payload = [
            'intent' => 'CAPTURE',
            'purchase_units' => [[
                'description' => $detalle,
                'amount' => [
                    'currency_code' => 'USD',
                    'value' => number_format($precio, 2, '.', '')
                ],
            ]],
            'application_context' => [
                'shipping_preference' => 'NO_SHIPPING'
            ]
        ];

        $ch = curl_init(paypal_base_url() . '/v2/checkout/orders');
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
                'Authorization: Bearer ' . $paypalToken
            ],
            CURLOPT_POSTFIELDS => json_encode($payload)
        ]);

        $res = curl_exec($ch);
        curl_close($ch);

        echo $res; // devuelve {"id": "..."}
        exit;
    }



    public static function capturarPaypal(){
        // Capturar el pago después de la aprobación
        header('Content-Type: application/json; charset=utf-8');
        session_start();

        // Obtener datos del POST
        $input = json_decode(file_get_contents('php://input'), true) ?? [];
        $orderID = $input['orderID'] ?? null;
        $token   = trim($input['token'] ?? '');
        $paqueteId = (int)($input['paquete_id'] ?? 0);

        if (!$orderID || $token === '' || !in_array($paqueteId, [1, 2], true)) {
            http_response_code(400);
            echo json_encode(['error' => 'Datos inválidos']);
            exit;
        }
        
        $paypalToken = paypal_access_token();

        $ch = curl_init(paypal_base_url() . "/v2/checkout/orders/{$orderID}/capture");
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
                'Authorization: Bearer ' . $paypalToken
            ],
            CURLOPT_POSTFIELDS => '{}'
        ]);

        // Ejecutar la petición
        $res = curl_exec($ch);
        curl_close($ch);

        $data = json_decode($res, true);

        $captureId = $data['purchase_units'][0]['payments']['captures'][0]['id'] ?? null;
        $status    = $data['status'] ?? null;
        $value     = $data['purchase_units'][0]['payments']['captures'][0]['amount']['value'] ?? null;

        if ($status !== 'COMPLETED' || !$captureId) {
            http_response_code(500);
            echo json_encode(['error' => 'Pago no completado']);
            exit;
        }

        // Validar monto
        $esperado = $paqueteId === 1 ? '199.00' : '49.00';
        if ($value !== $esperado) {
            http_response_code(400);
            echo json_encode(['error' => 'Monto inválido']);
            exit;
        }

        // ✅ Crear/actualizar registro AHORA (pago confirmado)
        $registro = Registro::where('token', $token);

        if (!$registro) {
            $registro = new Registro([
                'paquete_id' => $paqueteId,
                'pago_id'    => $captureId,
                'token'      => $token,
                'usuario_id' => $_SESSION['id']
            ]);
        } else {
            $registro->paquete_id = $paqueteId;
            $registro->pago_id = $captureId;
            $registro->usuario_id = $_SESSION['id'];
        }

        $registro->guardar();

        echo json_encode([
            'ok' => true,
            'token' => $token,
            'pago_id' => $captureId
        ]);
        exit;
    }

    public static function boleto(Router $router)
    {

        $id = $_GET['id'];
        if (!$id || !strlen($id) === 8) {
            header('Location: /');
            return;
        }

        $registro = Registro::where('token', $id);
        if (!$registro) {
            header('Location: /');
            return;
        }

        //llenar tabla de referencias
        $registro->usuario = Usuario::find($registro->usuario_id);
        $registro->paquete = Paquete::find($registro->paquete_id);

        // Render a la vista 
        $router->render('registro/boleto', [
            'titulo' => 'Asistencia a DevWebCamp',
            'registro' => $registro
        ]);
    }

    public static function conferencias(Router $router){
        if(!isAuth()){
            header('Location: /login');
            exit;
        }

        $usuario_id = $_SESSION['id'];
        $registro = Registro::where('usuario_id', $usuario_id);

        if(isset($registro) && $registro->paquete_id === "2"){
            header('Location: /boleto?id=' . urlencode($registro->token));
            return;
        }

        if(!$registro->paquete_id === "1"){
            header('Location: /');
            return;
        }
        
        if(isset($registro->regalo_id) && $registro->paquete_id === "1"){
            header('Location: /boleto?id=' . urlencode($registro->token));
            return;
        }

        $eventos = Evento::ordenar('hora_id', 'ASC');

        $eventos_formatedos = [];
        foreach($eventos as $evento){
            $evento->categoria = Categoria::find($evento->categoria_id);
            $evento->dia = Dia::find($evento->dia_id);
            $evento->hora = Hora::find($evento->hora_id);
            $evento->ponente = Ponente::find($evento->ponente_id);

            if($evento->dia_id === "1" && $evento->categoria_id === "1" ){
                $eventos_formatedos['conferencias_viernes'][] = $evento;
            }

            if($evento->dia_id === "2" && $evento->categoria_id === "1" ){
                $eventos_formatedos['conferencias_sabado'][] = $evento;
            }

            if($evento->dia_id === "1" && $evento->categoria_id === "2" ){
                $eventos_formatedos['workshops_viernes'][] = $evento;
            }

            if($evento->dia_id === "2" && $evento->categoria_id === "2" ){
                $eventos_formatedos['workshops_sabado'][] = $evento;
            }
        }


        $regalos = Regalo::all('ASC');

        //Registro Mediante POST
        if($_SERVER['REQUEST_METHOD'] === 'POST'){
            if(!isAuth()){
                header('Location: /login');
                exit;
            }

            $eventos = explode(',', $_POST['eventos']);
            if(empty($eventos) || count($eventos) > 5){
                echo json_encode(['resultado' => false]);
                return;
            }

            //Obtener el registro del usuario
            $registro = Registro::where('usuario_id', $_SESSION['id']);
            if(!isset($registro) || $registro->paquete_id !== "1"){
                echo json_encode(['resultado' => false]);
                return;
            }
            
            $eventos_array = [];
            //Validar la disponibilidad de los eventos
            foreach($eventos as $evento_id){
                $evento = Evento::find($evento_id);

                if(!isset($evento) || $evento->disponibles === "0"){
                    echo json_encode(['resultado' => false]);
                    return;
                }
                $eventos_array[] = $evento;
            }

            foreach($eventos_array as $evento){
                $evento->disponibles -= 1;
                $evento->guardar();
                //Almacenar el registro del evento y el usuario
                $datos = [
                    'evento_id' => (int) $evento->id,
                    'registro_id' => (int) $registro->id
                ];

                    $registro_usuario = new EventosRegistros($datos);
                    $registro_usuario->guardar();
                }

                //Actualizar el regalo del usuario
                $registro->sincronizar(['regalo_id' => $_POST['regalo_id']]);
                $resultado = $registro->guardar();

                if($resultado){
                    echo json_encode(['resultado' => $resultado, 'token' => $registro->token]);
                } else {
                    echo json_encode(['resultado' => false]);
                }

                return;
        }

        // Render a la vista 
        $router->render('registro/conferencias', [
            'titulo' => 'Elige Workshops y Conferencias',
            'eventos' => $eventos_formatedos,
            'regalos' => $regalos
        ]);
    }
}
