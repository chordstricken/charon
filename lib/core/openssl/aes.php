<?php
namespace core\openssl;

use \Exception;

/**
 * Index class responsible storing a list of all items
 * @author Jason Wright <jason.dee.wright@gmail.com>
 * @since 2/18/15
 * @package charon
 */
class AES {

    /** Default Encryption Mode */
    const METHOD = 'AES-256-CBC';

    /**
     * @param $data
     * @param string $key
     * @return string
     */
    public static function encrypt($data, $key = CRYPT_KEY) {

        // check if the key is base64
        if ($decoded_key = base64_decode($key, true))
            $key = $decoded_key;

        // set the params
        $data   = json_encode($data);
        $iv     = self::_iv();
        $cipher = openssl_encrypt($data, self::METHOD, $key, 0, $iv);

        return json_encode([
            'iv'     => base64_encode($iv),
            'cipher' => $cipher,
        ]);
    }

    /**
     * @param $obj
     * @param string $key
     * @return mixed
     * @throws Exception
     */
    public static function decrypt($obj, $key = CRYPT_KEY) {

        // check if the key is base64
        if ($decoded_key = base64_decode($key, true))
            $key = $decoded_key;

        // Verify item is a json object
        if (is_string($obj) && !$obj = json_decode($obj))
            throw new Exception(__METHOD__ . ' Failed to decode JSON', 500);

        // verify crypt is set
        if (!isset($obj->cipher))
            throw new Exception(__METHOD__ . ' Decryption object missing cipher param', 500);

        // verify iv is set
        if (!isset($obj->iv))
            throw new Exception(__METHOD__ . ' Decryption object missing iv param', 500);

        $obj->iv = base64_decode($obj->iv);

        // decrypt
        return json_decode(openssl_decrypt($obj->cipher, self::METHOD, $key, 0, $obj->iv));

    }

    /**
     * Generates the Initialization Vector
     */
    private static function _iv() {
        return openssl_random_pseudo_bytes(openssl_cipher_iv_length(self::METHOD));
    }

    /**
     * Generates a random encryption key
     */
    public static function getRandomKey() {
        return base64_encode(hash_pbkdf2("sha256", openssl_random_pseudo_bytes(32), uniqid(), 1000, 32, true));
    }

}

