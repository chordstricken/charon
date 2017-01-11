<?php
namespace test\core;
/**
 *
 * @author Jason Wright <jason.dee.wright@gmail.com>
 * @since 1/11/17
 * @package charon
 */

require_once(__DIR__ . '/../../../core.php');

use core;

class SQLiteTest extends \PHPUnit_Framework_TestCase {

    /**
     * Tests instantiating a read connection
     */
    public function testReadConnection() {
        echo __METHOD__ . PHP_EOL;
       $db = core\SQLite::initRead();
       $this->assertInstanceOf('core\\SQLite', $db);
    }

    /**
     * Tests instantiating a write connection
     */
    public function testWriteConnection() {
        echo __METHOD__ . PHP_EOL;
        $db = core\SQLite::initWrite();
        $this->assertInstanceOf('core\\SQLite', $db);
    }

    /**
     * Tests instantiating multiple Read instances
     */
    public function testMultiRead() {
        echo __METHOD__ . PHP_EOL;
        $one = core\SQLite::initRead()->querySingle('SELECT 1');
        $two = core\SQLite::initRead()->querySingle('SELECT 2');

        $this->assertEquals(1, $one, 'First read init failed');
        $this->assertEquals(2, $two, 'Second read init failed');
    }

    /**
     * Tests instantiating multiple Write instances and using them
     */
    public function testMultiWrite() {
        echo __METHOD__ . PHP_EOL;

        $one = core\SQLite::initWrite()->querySingle('SELECT 1');
        $two = core\SQLite::initWrite()->querySingle('SELECT 2');

        $this->assertEquals(1, $one, 'First write init failed');
        $this->assertEquals(2, $two, 'Second write init failed');

        core\SQLite::initWrite()->exec('CREATE TABLE IF NOT EXISTS test (id INT, field TEXT)');
        core\SQLite::initWrite()->exec('INSERT INTO test VALUES (1, \'one\')');
        core\SQLite::initWrite()->exec('INSERT INTO test VALUES (2, \'two\')');

        $one = core\SQLite::initWrite()->querySingle('SELECT id FROM test WHERE id = 1');
        $two = core\SQLite::initWrite()->querySingle('SELECT id FROM test WHERE id = 2');

        $this->assertEquals(1, $one, 'First write failed');
        $this->assertEquals(2, $two, 'Second write failed');

        core\SQLite::initWrite()->exec('DROP TABLE test');
    }

    /**
     * Tests instantiating a Write and a Read object
     */
    public function testDoubleInstance() {
        echo __METHOD__ . PHP_EOL;
        $one = core\SQLite::initWrite()->querySingle('SELECT 1');
        $two = core\SQLite::initRead()->querySingle('SELECT 2');

        $this->assertEquals(1, $one, 'Write init failed');
        $this->assertEquals(2, $two, 'Read init failed');
    }
}
