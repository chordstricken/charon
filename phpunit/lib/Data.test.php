<?php
/**
 * PHPUnit test for Data class
 */
require_once(__DIR__ . '/../../config.php');

class Data_test extends PHPUnit_Framework_TestCase {

    /**
     * Standard encrypt/decrypt test
     */
    public function test_create() {
        for ($i = 0; $i < 500; $i++) {
            $id = uniqid();
            $obj = (object)[
                "name" => "PHPUNIT-$id",
                "note" => hash('sha512', time()),
                "items" => [
                    [
                        "title" => "ljh",
                        "user" => "lkjghlkjgl",
                        "pass" => "hkglhg",
                        "note" => ""
                    ],
                    [
                        "title" => "alskdfjlekj",
                        "user" => "lkjh",
                        "pass" => "lkgh",
                        "note" => ""
                    ],
                    [
                        "title" => "alskdfjlekj",
                        "user" => "lkjh",
                        "pass" => "lkgh",
                        "note" => ""
                    ]
                ]
            ];

            Data::write($obj);
        }
    }

}