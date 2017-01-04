<?php
/**
 * Index class responsible storing a list of all items
 * @author Jason Wright <jason.dee.wright@gmail.com>
 * @since 2/18/15
 * @package charon
 */

class User {

    /** @var string */
    public static $name;

    /** @var string */
    public static $pass;

    /** @var string */
    public static $key;

    /** @var string filename */
    const ID = '_users';

    /** @var stdClass index */
    private static $_users;

    /**
     * Checks user authentication status
     * @throws Exception 401
     */
    public static function check_auth() {
        if (isset($_SESSION['name'], $_SESSION['key'])) {
            self::$name = $_SESSION['name'];
            self::$key = $_SESSION['key'];
            return;
        }

        throw new Exception('Please log in', 401);
    }

    /**
     * Authenticates a user
     * @param $user
     * @return bool
     */
    public static function login($user) {

        // decrypt
        if (isset($user->iv))
            $user = Crypt::dec($user, session_id());

        \Debug::info($user);

        // validate params
        if (empty($user->name))
            throw new Exception('Invalid User', 401);

        if (empty($user->pass))
            throw new Exception('Invalid Password', 401);

        if (empty($user->key))
            throw new Exception('Invalid Key', 401);

        self::read();

        if (!isset(self::$_users->{$user->name}))
            throw new Exception('Incorrect User', 401);

        $stored_password = self::$_users->{$user->name};

        // check if the password is old and needs rehashing
        if (password_needs_rehash($stored_password, PASSWORD_DEFAULT)) {
            $stored_password = password_hash($stored_password, PASSWORD_DEFAULT);
            self::$_users->{$user->name} = $stored_password;
            self::write();
        }

        \Debug::info($user->pass . ' VS ' . $stored_password);
        if (!password_verify($user->pass, $stored_password))
            throw new Exception('Incorrect Password', 401);

        self::$name = $_SESSION['name'] = $user->name;
        self::$pass = $_SESSION['key']  = $user->key;

        Request::send('Success');
    }

    /**
     * Retrieves the Index
     * @return stdClass
     */
    public static function read() {
        if (!self::$_users instanceof stdClass) {
            try {
                self::$_users = File::read(self::ID);

            } catch (Exception $e) {
                self::$_users = new stdClass();
                File::write(self::ID, self::$_users);

            }
        }
        return self::$_users;
    }

    /**
     * Saves the Index
     * @return stdClass $object
     * @throws Exception
     */
    public static function write() {
        if (!self::$_users instanceof stdClass) {
            return;
        }

        File::write(self::ID, self::$_users);
    }

    /**
     * Adds an item to the index
     * @param string $user
     * @param string $pass
     */
    public static function add($user, $pass) {
        self::read();
        self::$_users->{$user} = password_hash($pass, PASSWORD_DEFAULT);
        self::write();
    }

    /**
     * Removes an item from the index
     * @param string $user
     */
    public static function remove($user) {
        self::read();
        unset(self::$_users->{$user});
        self::write();
    }

}

