<?php
namespace core;

use api;
use \Exception;

/**
 * Base router class
 * @author Jason Wright <jason@silvermast.io>
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
        $head = array_shift($path); // class name / url path

        if (isset(self::$_routes[$head])) {
            $router = new self::$_routes[$head]($path);

        } elseif (class_exists($class = 'api\\' . ucwords(strtolower($head)))) {
            $router = new $class($path);

        } else {
            Response::send('Page not found.', 404);
        }

        $method = strtolower($_SERVER['REQUEST_METHOD']);

        if (!method_exists($router, $method))
            Response::send('Method does not exist.', 405);

        try {
            $router->$method();
        } catch (Exception $e) {
            Response::send($e->getMessage(), $e->getCode());
        }

        Response::send('An error occurred while we were routing your request.', 500);
    }

}