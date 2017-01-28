<?php
namespace test\models;

/**
 *
 * @author Jason Wright <jason@silvermast.io>
 * @since 1/5/17
 * @package charon
 */
require_once(__DIR__ . '/../../../core.php');


use models\Locker;
use \Exception;
use \PHPUnit_Framework_TestCase;

class LockerTest extends PHPUnit_Framework_TestCase {

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
                "note" => hash('sha512', time()),
                "items" => [
                    [
                        "title" => "ljh",
                        "user" => "lkjghlkjgl",
                        "pass" => "hkglhg",
                        "note" => "",
                    ],
                    [
                        "title" => "alskdfjlekj",
                        "user" => "lkjh",
                        "pass" => "lkgh",
                        "note" => "",
                    ],
                    [
                        "title" => "alskdfjlekj",
                        "user" => "lkjh",
                        "pass" => "lkgh",
                        "note" => "",
                    ],
                ]
            ];
        }
    }

    /**
     * Standard encrypt/decrypt test
     */
    public function testWrite() {
        foreach (self::$_data as &$obj) {
            $obj = Locker::new(get_object_vars($obj))->save();
            $this->assertNotNull($obj->id, "Failed to auto-assign ID hash");
        }
    }

    /**
     * Tests reading auto-generated test objects
     * @depends testWrite
     */
    public function testFindOne() {
        foreach (self::$_data as $obj) {
            $dbObj = Locker::findOne(['id' => $obj->id]);
            $this->assertEquals($obj->name, $dbObj->name, "DB object does not match the one in memory\n" . print_r([$obj, $dbObj], true));
        }
    }

    /**
     * Tests reading auto-generated test objects
     * @depends testWrite
     */
    public function testFind() {
        $dbObjects = Locker::findMulti(['name' => ['LIKE' => 'PHPUNIT-%']]);
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
                $dbObj = Locker::findOne(['id' => $obj->id]);
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
        \core\SQLite::initWrite()->exec('DELETE FROM locker WHERE name LIKE "PHPUNIT-%"');
    }
}
