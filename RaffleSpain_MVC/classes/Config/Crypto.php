<?php

/**
 * La clase Crypto proporciona funciones para encriptar y desencriptar datos utilizando AES-256-CBC.
 */
class Crypto {
    /**
     * Clave para la encriptación y desencriptación.
     */
    private static $key = "R@ffl3Sp@1nTM";
    
    /**
     * Encripta los datos utilizando AES-256-CBC.
     *
     * @param string $data Los datos a encriptar.
     * @return string Los datos encriptados en formato base64.
     */
    public static function encrypt_hash($data) {
        $ivSize = openssl_cipher_iv_length('aes-256-cbc');
        $iv = openssl_random_pseudo_bytes($ivSize);
        $encryptedData = openssl_encrypt($data, 'aes-256-cbc', self::$key, OPENSSL_RAW_DATA, $iv);
        return base64_encode($iv . $encryptedData);
    }
    
    /**
     * Desencripta los datos encriptados con AES-256-CBC.
     *
     * @param string $encryptedData Los datos encriptados en formato base64.
     * @return string Los datos desencriptados.
     */
    public static function decrypt_hash($encryptedData) {
        $encryptedData = base64_decode($encryptedData);
        $ivSize = openssl_cipher_iv_length('aes-256-cbc');
        $iv = substr($encryptedData, 0, $ivSize);
        $data = openssl_decrypt(substr($encryptedData, $ivSize), 'aes-256-cbc', self::$key, OPENSSL_RAW_DATA, $iv);
        return $data;
    }
}
