<?php
/**
 * PHPUnit test for Crypt class
 */
require_once(__DIR__ . '/../../config.php');

class Crypt_test extends PHPUnit_Framework_TestCase {

    /**
     * Standard encrypt/decrypt test
     */
    public function test_basic() {
        $str = 'This is a test string which is going to be encrypted';
        $enc = Crypt::enc($str);
        $dec = Crypt::dec($enc);

        $this->assertEquals($str, $dec, 'Encryption or Decryption failed');
    }

}