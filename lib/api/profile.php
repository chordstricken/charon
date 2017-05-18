<?php
namespace api;

use core;
use models;
use \Exception;
use \stdClass;

/**
 * /profile API path
 * @author Jason Wright <jason@silvermast.io>
 * @since 1/3/17
 * @package charon
 */
class Profile extends core\APIRoute {

    protected $is_encrypted = true;

    /**
     * GET /profile
     * Reads the authenticated user's profile
     */
    public function get() {
        if (!$this->is_json) {
            require(HTML . '/profile.php');
            die();

        } else {
            // load the locker data object
            $this->send(models\User::me());

        }
    }

    /**
     * POST /profile
     * Saves the user's profile information
     * @throws Exception
     */
    public function post() {
        if (!$this->data instanceof stdClass)
            throw new Exception('Invalid Request Object', 400);

        unset($this->data->id, $this->data->accountId, $this->data->permLevel); // avoid spoofing these variables

        // check if email already exists
        $existingUserCount = models\User::count([
            'accountId' => models\Account::current()->id,
            'id'        => ['$ne' => models\User::me()->id],
            'email'     => $this->data->email,
        ]);
        if ($existingUserCount)
            throw new Exception('A User already exists with this email address.', 400);


        if (empty($this->data->passhash))
            unset($this->data->passhash);

        if (empty($this->data->contentKeyEncrypted))
            unset($this->data->contentKeyEncrypted);

        $user = models\User::me();
        $user->setVars($this->data);
        $user->validate()->save();

        $this->send($user);
    }

}