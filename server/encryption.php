<?php
define('SECURE_ACCESS', true);
$config = include __DIR__ . '/config.php';
$secret_key = $config['security']['secret_key'];
$cipher = $config['security']['cipher'];

function encryptData($data, $secret_key, $cipher) {
    $iv_length = openssl_cipher_iv_length($cipher);
    $iv = openssl_random_pseudo_bytes($iv_length);
    $encrypted = openssl_encrypt($data, $cipher, $secret_key, 0, $iv);
    return base64_encode($iv . $encrypted);
}

function decryptData($encryptedData, $secret_key, $cipher) {
    $encryptedData = base64_decode($encryptedData);
    $iv_length = openssl_cipher_iv_length($cipher);
    $iv = substr($encryptedData, 0, $iv_length);
    $encrypted = substr($encryptedData, $iv_length);
    return openssl_decrypt($encrypted, $cipher, $secret_key, 0, $iv);
}
?>
