<?php

function paypal_base_url(): string {
    return ($_ENV['PAYPAL_MODE'] ?? 'sandbox') === 'live'
        ? 'https://api-m.paypal.com'
        : 'https://api-m.sandbox.paypal.com';
}

function paypal_access_token(): string {
    $client = $_ENV['PAYPAL_CLIENT_ID'] ?? '';
    $secret = $_ENV['PAYPAL_SECRET'] ?? '';

    if ($client === '' || $secret === '') {
        throw new \Exception("Faltan PAYPAL_CLIENT_ID o PAYPAL_SECRET en .env");
    }

    $url = paypal_base_url() . '/v1/oauth2/token';

    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true,
        CURLOPT_USERPWD => $client . ':' . $secret,
        CURLOPT_POSTFIELDS => 'grant_type=client_credentials',
        CURLOPT_HTTPHEADER => [
            'Accept: application/json',
            'Accept-Language: en_US',
        ],
    ]);

    $response = curl_exec($ch);

    if ($response === false) {
        $errno = curl_errno($ch);
        $err = curl_error($ch);
        curl_close($ch);
        throw new \Exception("cURL token error ({$errno}): {$err}");
    }

    $http = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($http < 200 || $http >= 300) {
        throw new \Exception("PayPal token HTTP {$http}: {$response}");
    }

    $data = json_decode($response, true);
    if (!is_array($data) || empty($data['access_token'])) {
        throw new \Exception("PayPal token response inválida: {$response}");
    }

    return $data['access_token'];
}
