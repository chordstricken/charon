<?php
namespace core;

use api;
use \Exception;

/**
 * Base router class
 * @author Jason Wright <jason.dee.wright@gmail.com>
 * @since 1/2/17
 * @package charon
 */
class Router {

    /**
     * @var array
     */
    private static $_routes = [
        'handshake' => 'api\\Handshake',
        'login'     => 'api\\Login',
        'logout'    => 'api\\Logout',
        'locker'    => 'api\\Locker',
        'user'      => 'api\\User',
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

        if (!isset(self::$_routes[$head]))
            Response::send('Page not found.', 404);

        $router = new self::$_routes[$head]($path);
        $method = strtolower($_SERVER['REQUEST_METHOD']);

        if (!method_exists($router, $method))
            Response::send('Method does not exist.', 405);

        try {
            $router->$method();
        } catch (Exception $e) {
            Response::send($e->getMessage(), $e->getCode());
        }
        die();
    }

}