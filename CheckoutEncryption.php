<?php

// Alternatively configure this on your web server(apache/nginx) to avoid CORS error
header("Access-Control-Allow-Origin: *");

class CheckoutEncryption {

    private $accessKey;
    private $ivKey;
    private $secretKey;
    private $request;

    public function __construct() {
        $this->accessKey = '$2a$08$mxn8tRQougsSnTB940lecuUVy6z5JbCC174ERp8LIuTiZqmBGTgau';
        $this->ivKey = "8p7H4xZ9qtmdjz3P";
        $this->secretKey = "jyWcZLRr7xqKQ2Cp";
        $this->request = json_decode(file_get_contents('php://input'), true);
    }

    public function processEncryption() {
        $encryptedParams = $this->encrypt($this->ivKey, $this->secretKey, $this->request);
        $result = [
            'params' => $encryptedParams,
            'accessKey' => $this->accessKey,
            'countryCode' => $this->request['countryCode']
        ];

        echo json_encode($result);
    }

    /**
     * Encrypt the string containing customer details with the IV and secret
     * key provided in the developer portal
     *
     * @return $encryptedPayload
     */
    public function encrypt($ivKey, $secretKey, $payload = []) {
        //The encryption method to be used
        $encrypt_method = "AES-256-CBC";

        // Hash the secret key
        $key = hash('sha256', $secretKey);

        // Hash the iv - encrypt method AES-256-CBC expects 16 bytes
        $iv = substr(hash('sha256', $ivKey), 0, 16);
        $encrypted = openssl_encrypt(
            json_encode($payload, true), $encrypt_method, $key, 0, $iv
        );

        //Base 64 Encode the encrypted payload
        $encryptedPayload = base64_encode($encrypted);

        return $encryptedPayload;
    }

}

$class = new CheckoutEncryption();
$class->processEncryption();