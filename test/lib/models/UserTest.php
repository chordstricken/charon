<?php
namespace test\models;

/**
 *
 * @author Jason Wright <jason.dee.wright@gmail.com>
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
        $dbObjects = User::findMulti(['name' => ['LIKE' => 'PHPUNIT-%']]);
        $this->assertCount(count(self::$_data), $dbObjects, 'Did not find correct number of objects with PHPUNIT-% name');

        foreach (self::$_data as $obj)
            $this->assertArrayHasKey($obj->id, $dbObjects, 'Failed to pull multiple objects from database');
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
        \core\SQLite::initWrite()->exec('DELETE FROM user WHERE name LIKE "PHPUNIT-%"');
    }
}
