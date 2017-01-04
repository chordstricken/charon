<?php
namespace api;

use \User;

/**
 * Handles a login request
 * @author Jason Wright <jason.dee.wright@gmail.com>
 * @since 1/2/17
 * @package charon
 */
class Login extends Base {

    /** @var bool overrides parent */
    protected $require_auth = false;

    public function post() {
        User::login($this->data);
    }

}