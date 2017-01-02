<?php
/**
 * Index class responsible storing a list of all items
 * @author Jason Wright <jason.dee.wright@gmail.com>
 * @since 2/18/15
 * @package charon
 */

class User {

    /**
     * @var string
     */
    public static $name;

    /**
     * @var string
     */
    public static $pass;

    /**
     * @var string filename
     */
    const ID = '_users';

    /**
     * @var stdClass index
     */
    private static $_users;

    /**
     * Checks user authentication status
     * @throws Exception 401
     */
    public static function check_auth() {
        if (isset($_SESSION['name'], $_SESSION['pass'])) {
            self::$name = $_SESSION['name'];
            self::$pass = $_SESSION['pass'];
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

        // validate params
        if (empty($user->name))
            throw new Exception('Invalid User', 401);

        if (empty($user->pass))
            throw new Exception('Invalid Password', 401);

        self::read();

        if (!isset(self::$_users->{$user->name}))
            throw new Exception('Incorrect User', 401);

        if (self::$_users->{$user->name} != $user->pass)
            throw new Exception('Incorrect Password', 401);

        self::$name = $_SESSION['name'] = $user->name;
        self::$pass = $_SESSION['pass'] = $user->pass;

        Request::send('Success');
    }

    /**
     * Destroys the session and throws a 401
     * @throws Exception 401
     */
    public static function logout() {
        unset($_SESSION);
        session_destroy();
        throw new Exception('Suggessfully logged out.', 401);
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
        self::$_users->{$user} = hash('sha256', $pass);
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

