<?php
namespace core;

/**
 * @author Jason Wright <jason@silvermast.io>
 * @since 1/4/17
 * @package charon
 */
trait Singleton {

    /** @var static */
    protected static $_instance;

    /**
     * Returns a single instance
     * @return static
     */
    public static function init($opts = null) {
        if (!static::$_instance) static::$_instance = new static($opts);
        return static::$_instance;
    }

    /**
     * Generates a fresh instance every time
     * @return static
     */
    public static function new($opts = null) {
        return new static($opts);
    }

}