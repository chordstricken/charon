<?php
namespace api;

use models;
use \Exception;
use core;

/**
 * Handles a login request
 * @author Jason Wright <jason@silvermast.io>
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
//        $this->is_encrypted = true;
//        $this->decryptPayload();
//        core\Debug::info($this->data);

        if (isset($this->data->iv))
            $this->data = core\openssl\AES::decrypt($this->data, $_SESSION['AESKey']);

        // validate params
        if (empty($this->data->name)) throw new Exception('Invalid User', 401);
        if (empty($this->data->pass)) throw new Exception('Invalid Password', 401);

        // Pull the user list into memory
        if (!$dbUser = models\User::findOne(['name' => $this->data->name]))
            throw new Exception('Incorrect User', 401);

        // check if the password is old and needs rehashing
        if (password_needs_rehash($dbUser->passhash, PASSWORD_DEFAULT)) {
            $dbUser->passhash = password_hash($dbUser->passhash, PASSWORD_DEFAULT);
            $dbUser->save();
        }

        if (!password_verify($this->data->pass, $dbUser->passhash)) throw new Exception('Incorrect Password', 401);

        $_SESSION['user_id'] = $dbUser->id;

        $this->send('Success', 200, true);

    }

}