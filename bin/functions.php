<?php
/**
 *
 * @author Jason Wright <jason.dee.wright@gmail.com>
 * @since 2/24/17
 * @package charon
 */

/**
 * @param null $msg
 * @param int $code
 */
function finish($msg = null, $code = 0) {
    echo ($msg ?? "Exiting") . "\n";
    die($code);
}

/**
 * Masked readline. Useful for passwords
 * @param $prompt
 * @return string
 */
function readline_masked($prompt) {
    $result = trim(`/bin/bash -c "read -s -p '$prompt' result && echo \\\$result"`);
    echo "\n";
    return $result;
}

/**
 * extension of User class specifically for CLI functionality
 * @author Jason Wright <jason.dee.wright@gmail.com>
 * @since 5/9/2017
 */
class CLIUser extends models\User {

    public $contentKey;

    /** @var self */
    public static $_me;

    /**
     * return self
     */
    public static function me() {
        if (isset(static::$_me)) return static::$_me;

        if (!$email = trim(readline('Your email: ')))
            throw new Exception('Invalid Email', 401);

        if (!$pass = readline_masked('Your password: '))
            throw new Exception('Invalid Password', 401);

        $query = [
            'email'     => $email,
            'accountId' => models\Account::current()->id,
        ];

        // Pull the user list into memory
        if (!$dbUser = self::findOne($query))
            throw new Exception('Incorrect User', 401);

        if ($dbUser->passhash != models\User::hashPassword($pass))
            throw new Exception('Incorrect Password', 401);

        $dbUser->contentKey = $dbUser->getContentKey($pass);

        return static::$_me = $dbUser;
    }

    public function __destruct() {
        unset($this->contentKey);
    }

    public function save() {
        $contentKey = $this->contentKey;
        unset($this->contentKey);
        $result = parent::save();
        $this->contentKey = $contentKey;
        return $result;
    }
}