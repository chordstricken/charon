<?php
namespace api;

use models;
use \Exception;
use core;

/**
 * Handles a login request
 * @author Jason Wright <jason.dee.wright@gmail.com>
 * @since 1/2/17
 * @package charon
 */
class Login extends Base {

    /** @var bool overrides parent */
    protected $require_auth = false;

    /**
     * Authenticates the user
     */
    public function post() {
        $reqUser = $this->data;

        // decrypt
        if (isset($reqUser->iv))
            $reqUser = core\Crypt::dec($reqUser, session_id());

        // validate params
        if (empty($reqUser->name)) throw new Exception('Invalid User', 401);
        if (empty($reqUser->pass)) throw new Exception('Invalid Password', 401);
        if (empty($reqUser->key))  throw new Exception('Invalid Key', 401);

        // Pull the user list into memory
        if (!$dbUser = models\User::findOne(['name' => $reqUser->name]))
            throw new Exception('Incorrect User', 401);

        // check if the password is old and needs rehashing
        if (password_needs_rehash($dbUser->passhash, PASSWORD_DEFAULT)) {
            $dbUser->passhash = password_hash($dbUser->passhash, PASSWORD_DEFAULT);
            $dbUser->save();
        }

        if (!password_verify($reqUser->pass, $dbUser->passhash)) throw new Exception('Incorrect Password', 401);

        $_SESSION['user_id']   = $dbUser->id;
        $_SESSION['user_key']  = $reqUser->key; // AES encryption key.

        $this->send('Success');

    }

}