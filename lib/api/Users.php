<?php
namespace api;

use core;
use models;
use \Exception;
use \stdClass;

/**
 * /manage-users API path
 * @author Jason Wright <jason@silvermast.io>
 * @since 1/3/17
 * @package charon
 */
class Users extends core\APIRoute {

    protected $is_encrypted = true;

    /**
     * GET /manage-users
     * Reads the authenticated user's manage-users
     */
    public function get() {
        $this->checkPermission([models\User::PERMLEVELS['Admin']]);

        if (!$this->is_json) {
            require(HTML . '/users.php');
            die();

        } else {

            if (isset($this->path[0])) {
                $this->send(models\User::findOne(['id' => $this->path[0]]));

            } else {
                // load the locker data object
                $this->send(models\User::findMulti([]));
            }

        }
    }

    /**
     * POST /manage-users/<user-id>
     * Saves the user's manage-users information
     * @throws Exception
     */
    public function post() {
        $this->checkPermission([models\User::PERMLEVELS['Admin']]);

        if (!$this->data instanceof stdClass)
            throw new Exception('Invalid Request Object', 400);

        if (isset($this->path[0]) && !$user = models\User::findOne(['id' => $this->path[0]]))
            throw new Exception('Unable to find that user.', 404);
        else $user = models\User::new();

        // check if email already exists

        // set password
        if (!empty($this->data->changePass1)) {
            if ($this->data->changePass1 !== $this->data->changePass2)
                throw new Exception('Passwords do not match', 400);
            else
                $user->setPasswordHash($this->data->changePass1);
        }

        // set other data
        $user->setVars($this->data);
        $user->accountId = models\Account::current()->id;

        $user->validate()->save();

        $this->send($user);
    }

    /**
     * DELETE /manage-users/<user-id>
     * @throws Exception
     */
    public function delete() {
        if (!isset($this->path[0]))
            throw new Exception('Invalid User ID', 400);

        if (!$user = models\User::findOne(['id' => $this->path[0]]))
            throw new Exception('Unable to find that user.', 404);

        $user->delete();
    }

}