<?php
namespace test\core\openssl;
/**
 *
 * @author Jason Wright <jason@silvermast.io>
 * @since 1/11/17
 * @package charon
 */

require_once(__DIR__ . '/../../../../core.php');

use core;
use \stdClass;

class RSATest extends \PHPUnit_Framework_TestCase {

    /** @var stdClass */
    private static $_client;
    /** @var stdClass */
    private static $_server;
    /** @var string */
    private static $_payload;

    /**
     * Tests creating a key
     */
    public static function setUpBeforeClass() {
        self::$_client  = core\openssl\RSA::createKeyPair();
        self::$_server  = core\openssl\RSA::createKeyPair();
        self::$_payload = uniqid();
    }

    /**
     * Tests client priv -> pub
     */
    public function testClientPrivToPub() {
        $cipher = core\openssl\RSA::encryptWithPriv(self::$_payload, self::$_client->private);
        $result = core\openssl\RSA::decryptWithPub($cipher, self::$_client->public);
        $this->assertEquals(self::$_payload, $result, 'client priv -> client pub failed');
    }

    /**
     * Tests client pub -> priv
     */
    public function testClientPubToPriv() {
        $cipher = core\openssl\RSA::encryptWithPub(self::$_payload, self::$_client->public);
        $result = core\openssl\RSA::decryptWithPriv($cipher, self::$_client->private);
        $this->assertEquals(self::$_payload, $result, 'client pub -> client priv failed');
    }

    /**
     * Tests server priv -> pub
     */
    public function testServerPrivToPub() {
        $cipher = core\openssl\RSA::encryptWithPriv(self::$_payload, self::$_server->private);
        $result = core\openssl\RSA::decryptWithPub($cipher, self::$_server->public);
        $this->assertEquals(self::$_payload, $result, 'server priv -> server pub failed');
    }

    /**
     * Tests server pub -> priv
     */
    public function testServerPubToPriv() {
        $cipher = core\openssl\RSA::encryptWithPub(self::$_payload, self::$_server->public);
        $result = core\openssl\RSA::decryptWithPriv($cipher, self::$_server->private);
        $this->assertEquals(self::$_payload, $result, 'server pub -> server priv failed');
    }

}
