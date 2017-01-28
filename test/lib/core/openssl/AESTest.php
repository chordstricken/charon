<?php
namespace test\core\openssl;
/**
 * @author Jason Wright <jason@silvermast.io>
 * @since 1/11/17
 * @package charon
 */

require_once(__DIR__ . '/../../../../core.php');

use core;

class AESTest extends \PHPUnit_Framework_TestCase {

    /**
     * Tests client priv -> pub
     */
    public function test() {
        $data = 'this is a very secure password that needs to be stored';

        $cipher = core\openssl\AES::encrypt($data);
        $result = core\openssl\AES::decrypt($cipher);

        $this->assertEquals($data, $result);

    }

}
