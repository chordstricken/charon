<?php
namespace api;

use core;
use models;
use \Exception;
use \stdClass;

/**
 * /account API path
 * @author Jason Wright <jason@silvermast.io>
 * @since 1/3/17
 * @package charon
 */
class Account extends core\APIRoute {

    /**
     * GET /account
     * Reads the authenticated user's account
     */
    public function get() {
        if (!$this->is_json) {
            require(HTML . '/account.php');
            die();

        } else {
            // load the locker data object
            $this->send(models\Account::current());

        }
    }

    /**
     * POST /account
     * Saves the user's account information
     * @throws Exception
     */
    public function post() {
        if (!$this->data instanceof stdClass)
            throw new Exception('Invalid Request Object', 400);

        unset($this->data->id); // avoid spoofing user id
        unset($this->data->slug);

        if ($obj = models\Account::current())
            $obj->setVars($this->data)->validate()->save();

        $this->send($obj);
    }

}