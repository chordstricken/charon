<?php
namespace api;

use models;
use \Exception;
use core\openssl\RSA;
use core\openssl\AES;

/**
 * Handles a RSA-encrypted AES key exchange
 * @author Jason Wright <jason.dee.wright@gmail.com>
 * @since 1/2/17
 * @package charon
 */
class Handshake extends Base {

    /** @var bool overrides parent */
    protected $require_auth = false;
    /** @var bool */
    protected $is_encrypted = false;

    /**
     * Receives an RSA encrypted
     */
    public function post() {

        if (!$clientPublicKey = RSA::decryptWithPriv($this->getPayload(), RSA::getServerKeyPair()->private))
            throw new Exception('Failed to decrypt the request.', 401);

        $symKey = AES::getRandomKey();
        $result = RSA::encryptWithPub($symKey, $clientPublicKey);

        // store the AES key in the session
        $_SESSION['AESKey'] = $symKey;

        $this->send($result, 200);

    }

}