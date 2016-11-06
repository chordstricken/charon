<?php
/**
 * PHPUnit test for Crypt class
 */
require_once(__DIR__ . '/../../core.php');

class Crypt_test extends PHPUnit_Framework_TestCase {

    public static function setUpBeforeClass() {
        ini_set('display_errors', 1);
    }

    /**
     * Standard encrypt/decrypt test
     */
    public function test_basic() {
        $str = 'This is a test string which is going to be encrypted';
        $enc = Crypt::enc($str);
        $dec = Crypt::dec($enc);

        $this->assertEquals($str, $dec, 'Encryption or Decryption failed');
    }

    /**
     * Tests a long string
     */
    public function test_huge() {
        $str = str_repeat(md5(microtime(true)), 1000000);
        $enc = Crypt::enc($str);
        $dec = Crypt::dec($enc);

        $this->assertEquals($str, $dec, 'Encryption failed for large string');
    }

}
