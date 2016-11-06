<?php
/**
 * PHPUnit test for Data class
 */
require_once(__DIR__ . '/../../core.php');

class Data_test extends PHPUnit_Framework_TestCase {

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
    public function test_write() {
        foreach (self::$_data as $obj) {
            Data::write($obj);
            $this->assertNotNull($obj->id, "Failed to auto-assign ID hash");
        }
    }

    /**
     * Tests reading auto-generated test objects
     * @depends test_write
     */
    public function test_read() {
        foreach (self::$_data as $obj) {
            $db_obj = Data::read($obj->id);
            $this->assertEquals($obj->name, $db_obj->name, "DB object does not match the one in memory");
        }
    }

    /**
     * Tests reading auto-generated test objects
     * @depends test_read
     */
    public function test_delete() {
        foreach (self::$_data as $obj) {
            Data::delete($obj->id);
            try {
                $db_obj = Data::read($obj->id);
                $this->fail("Found object '$db_obj->id' after deletion");

            } catch (Exception $e) {
                continue;
            }
        }
    }

}
