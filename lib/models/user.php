<?php
namespace models;

use \Exception;

/**
 *
 * @author Jason Wright <jason.dee.wright@gmail.com>
 * @since 1/4/17
 * @package charon
 */
class User extends Base {
    const TABLE = 'users';

    public $id;
    public $name;
    public $passhash;

    /** @var User */
    private static $_me = null;

    /**
     * @throws Exception
     */
    public function validate() {
        if (mb_strlen($this->id) > 1024)        throw new Exception('Invalid id');
        if (mb_strlen($this->name) > 1024)      throw new Exception('Invalid name');
        if (mb_strlen($this->passhash) > 1024)  throw new Exception('Invalid passhash');
        return $this;
    }

    /**
     * @return User|null
     */
    public static function me() {
        if (!isset($_SESSION['user_id']))
            return null;

        if (!self::$_me instanceof self)
            self::$_me = self::findOne(['id' => $_SESSION['user_id']]);

        return self::$_me;
    }

    /**
     * returns encryption key
     * @return mixed
     */
    public static function getKey() {
        return $_SESSION['user_key'];
    }

    /**
     * Hashes the password and sets it into the object
     * @param $password
     * @return self
     */
    public function setPasswordHash($password) {
        $this->passhash = password_hash($password, PASSWORD_DEFAULT);
        return $this;
    }
}