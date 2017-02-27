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
class Login extends core\APIRoute {

    /** @var bool overrides parent */
    protected $require_auth = false;

    /**
     * Authenticates the user
     */
    public function post() {

        if (isset($this->data->iv))
            $this->data = core\openssl\AES::decrypt($this->data, $_SESSION['AESKey']);

        // validate params
        if (empty($this->data->email)) throw new Exception('Invalid Email', 401);
        if (empty($this->data->pass)) throw new Exception('Invalid Password', 401);

        $query = [
            'email'     => $this->data->email,
            'accountId' => models\Account::current()->id,
        ];

        // Pull the user list into memory
        if (!$dbUser = models\User::findOne($query))
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