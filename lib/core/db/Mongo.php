<?php
namespace core\db;

use \core;
use \Exception;
use \MongoDB\Driver;

/**
 * MongoDB class
 * @author Jason Wright <jason.dee.wright@gmail.com>
 * @since 2/9/17
 * @package charon
 */
trait Mongo {

    /** @var \MongoDB\Driver\Manager */
    private static $_db;

    /** @var string */
    protected static $dbName = 'charon';

    /**
     * Database factory.
     * @return \MongoDB\Driver\Manager
     */
    private static function _db() {
        return self::$_db ?? self::$_db = new \MongoDB\Driver\Manager('mongodb://localhost:27017');
    }

    protected static function getDBNamespace() {
        return static::$dbName . '.' . static::TABLE;
    }

    /**
     * Executes an anonymous function and checks for database errors
     * @param $function
     * @throws Exception
     */
    private static function _handleDBException(Exception $e) {
        $exceptionClass = get_class($e);
        switch ($exceptionClass) {
            default:
                core\Debug::error($exceptionClass . ': ' . $e->getMessage());
                throw new Exception('There was an error communicating with the database', 500, $e);
        }
    }

}