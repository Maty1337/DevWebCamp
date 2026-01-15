<?php

namespace Controllers;

use MVC\Router;
use Model\Usuario;
use Model\Registro;
use Model\Paquete;

class RegistroController
{

    public static function crear(Router $router){

        if (!isAuth()) {
            header('Location: /');
            return;
        }

        $registro = Registro::where('usuario_id', $_SESSION['id']);

        // Si ya tiene un registro (gratis / virtual / presencial)
        if ($registro) {
            header('Location: /boleto?id=' . urlencode($registro->token));
            return;
        }

        // Si NO tiene registro todavía, generar token para esta compra/registro
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
}
