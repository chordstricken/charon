<?php
namespace api;

use \Request;

/**
 * Base router class
 * @author Jason Wright <jason.dee.wright@gmail.com>
 * @since 1/2/17
 * @package charon
 */
class Controller {

    /**
     * @var array
     */
    private static $_routes = [
        'login'  => 'api\\Login',
        'logout' => 'api\\Logout',
        'locker' => 'api\\Locker',
        'user'   => 'api\\User',
    ];

    /**
     * Routes the request to the corresponding subclass
     */
    public static function route() {
        if (!$path = trim($_SERVER['REQUEST_URI'], '/')) {
            header('Location: /locker');
            die();
        }

        $path = explode('/', $path);
        $head = array_shift($path);
        $data = Request::get();

        if (!isset(self::$_routes[$head]))
            Request::send('Page not found.', 404);

        $router = new self::$_routes[$head]($path, $data);
        $method = strtolower($_SERVER['REQUEST_METHOD']);

        if (!method_exists($router, $method))
            Request::send('Method does not exist.', 405);

        $router->$method();
        die();
    }

}