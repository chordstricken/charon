<?php
namespace core\openssl;

use \Exception;

/**
 *
 * @author Jason Wright <jason@silvermast.io>
 * @since 1/11/17
 * @package charon
 */
class RSA {

    /**
     * @var array
     */
    private static $_opts = [
        'digest_alg'       => 'sha512',
        'private_key_bits' => 4096,
        'private_key_type' => OPENSSL_KEYTYPE_RSA,
    ];

    /**
     * Creates a new key pair
     */
    public static function createKeyPair() {
        $keyPair = new \stdClass();

        // Create the keypair resource
        $rawResource = openssl_pkey_new(self::$_opts);

        // extract private key
        openssl_pkey_export($rawResource, $keyPair->private);

        // extract public key
        $details = openssl_pkey_get_details($rawResource);
        $keyPair->public = $details['key'];
        return $keyPair;
    }

    /**
     * Returns the server key pair for the current session
     */
    public static function getServerKeyPair() {
        return $_SESSION['serverKeyPair'] ?? $_SESSION['serverKeyPair'] = self::createKeyPair();
    }

    /**
     * @param $data
     * @param $key
     * @return String (base64 encoded hex)
     * @throws Exception
     */
    public static function encryptWithPub($data, $key) {
        if (!openssl_public_encrypt($data, $result, $key)) throw new Exception(openssl_error_string());
        return base64_encode(bin2hex($result));
    }

    /**
     * @param $data
     * @param $key
     * @return String (base64 encoded hex)
     * @throws Exception
     */
    public static function encryptWithPriv($data, $key) {
        if (!openssl_private_encrypt($data, $result, $key)) throw new Exception(openssl_error_string());
        return base64_encode(bin2hex($result));
    }

    /**
     * @param $data (base64 encoded hex)
     * @param $key
     * @return mixed
     * @throws Exception
     */
    public static function decryptWithPub($data, $key) {
        if (!$data = base64_decode($data)) throw new Exception('Failed to convert base64 into hex');
        if (!$data = hex2bin($data))       throw new Exception('Failed to convert hex into binary');
        if (!openssl_public_decrypt($data, $result, $key)) throw new Exception(openssl_error_string());
        return $result;
    }

    /**
     * @param $data (base64 encoded hex)
     * @param $key
     * @return mixed
     * @throws Exception
     */
    public static function decryptWithPriv($data, $key) {
        if (!$data = base64_decode($data)) throw new Exception('Failed to convert base64 into hex');
        if (!$data = hex2bin($data))       throw new Exception('Failed to convert hex into binary');
        if (!openssl_private_decrypt($data, $result, $key)) throw new Exception(openssl_error_string());
        return $result;
    }

}