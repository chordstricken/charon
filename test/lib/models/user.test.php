<?php
namespace test\models;

/**
 *
 * @author Jason Wright <jason@silvermast.io>
 * @since 1/5/17
 * @package charon
 */
require_once(__DIR__ . '/../../../core.php');


use models\User;
use \Exception;
use \PHPUnit_Framework_TestCase;

class UserTest extends PHPUnit_Framework_TestCase {

    /** @var array */
    private static $_data = [];

    /**
     * Sets up data before running
     */
    public static function setUpBeforeClass() {
        for ($i = 0; $i < 10; $i++) {
            $id = uniqid();
            self::$_data[] = (object)[
                "name" => "PHPUNIT-$id",
                "passhash" => md5(time()),
            ];
        }
    }

    /**
     * Standard encrypt/decrypt test
     */
    public function testWrite() {
        foreach (self::$_data as &$obj) {
            $obj = User::new($obj)->save();
            $this->assertNotNull($obj->id, "Failed to auto-assign ID hash");
        }
    }

    /**
     * Tests reading auto-generated test objects
     * @depends testWrite
     */
    public function testFindOne() {
        foreach (self::$_data as $obj) {
            $dbObj = User::findOne(['id' => $obj->id]);
            $this->assertEquals($obj->name, $dbObj->name, "DB object does not match the one in memory\n" . print_r([$obj, $dbObj], true));
        }
    }

    /**
     * Tests reading auto-generated test objects
     * @depends testWrite
     */
    public function testFind() {
        $regex = new \MongoDB\BSON\Regex('^PHPUNIT', 'i');
        $dbObjects = User::findMulti(['name' => $regex]);
        $this->assertCount(count(self::$_data), $dbObjects, 'Did not find correct number of objects with PHPUNIT-% name');

        foreach (self::$_data as $obj)
            $this->assertArrayHasKey($obj->id, $dbObjects, 'Failed to pull multiple objects from database');
    }

    /**
     * @depends testWrite
     */
    public function testContentKey() {
        $password   = 'i fear nothing';
        $contentKey = bin2hex(openssl_random_pseudo_bytes(256));

        $user_data = reset(self::$_data);
        $user = User::findOne(['id' => $user_data->id]);

        $user->setContentKey($password, $contentKey);
        $user->save();
        unset($user);

        $user = User::findOne(['id' => $user_data->id]);
        $dbContentKey = $user->getContentKey($password);
        echo "dbContentKey: $dbContentKey\n";
        echo "contentKey: $contentKey\n";

        $this->assertEquals($dbContentKey, $contentKey, 'Content Keys do not match');
    }

    /**
     * Tests reading auto-generated test objects
     * @depends testFindOne
     */
    public function testDelete() {
        foreach (self::$_data as $obj) {
            $obj->delete();
            try {
                $dbObj = User::findOne(['id' => $obj->id]);
                $this->fail("Found object '$dbObj->id' after deletion");

            } catch (Exception $e) {
                continue;
            }
        }
    }

    /**
     * Clean up database
     */
    public static function tearDownAfterClass() {
        $regex = new \MongoDB\BSON\Regex('^PHPUNIT', 'i');
        $dbObjects = User::findMulti(['name' => $regex]);
        foreach ($dbObjects as $obj)
            $obj->delete();
    }
}
