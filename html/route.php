<?php
/**
 * Charon Root file and router
 * @author Jason Wright <jason.dee.wright@gmail.com>
 * @since Feb 18, 2015
 * @copyright 2015 Jason Wright
 */

// load the config file
require_once(__DIR__ . '/../core.php');

// ajax layer
try {

    // start the session
    session_start();

    $path = explode('/', $_SERVER['REQUEST_URI']); // break url into fragments
    $path = array_filter($path, 'trim'); // pull out empty values
    $path = array_values($path); // reset array indexes after trimming

    // check authentication
    if (!count($path) || $path[0] != 'login')
        User::check_auth();

    // check path for idiots trying to do shit they shouldn't be doing
    if (isset($path[0]) && strpos($path[0], '.') !== false)
        throw new Exception('Page not found.', 404);


    // handle ajax call
    switch ($_SERVER['REQUEST_METHOD']) {

        /**
         * Retrieving main page, or pulling data
         */
        case 'GET':
            if (!count($path))
                break;

            switch ($path[0]) {

                case 'logout':
                    User::logout();
                    break;

                case Index::ID:
                    $index = Index::read();
                    $data  = [];
                    foreach ($index as $key => $value) {
                        $value->id = $key;
                        $data[$value->name] = $value;
                    }
                    ksort($data);
                    $data = array_values($data);
                    break;

                case 'search':
                    $query = trim($path[1]);

                    if (strlen($query) < 3) {
                        $data = Index::read();
                        break;
                    }

                    $data = Index::search($path[1]);
                    break;

                default:
                    $data = Data::read($path[0]);
                    break;

            }

            $data = Crypt::enc($data, User::$pass);

            // send the data
            Request::send($data);

        /**
         * Save an object. 200 Exception is typically thrown in Data class
         */
        case 'POST':
            $obj = Request::get();

            if (!$obj instanceof stdClass)
                throw new Exception('Invalid object', 400);

            if (isset($path[0]) && $path[0] == 'login')
                User::login($obj);

            // decrypt the request
            if (!$obj = Crypt::dec($obj, User::$pass))
                Request::send('Failed to decrypt request.', 500);

            // write the data
            Data::write($obj);

            // re-encrypt the result before sending it to the user
            $obj = Crypt::enc($obj, User::$pass);

            // send the response
            Request::send($obj);

        /**
         * Delete an object. 200 Exception is typcially thrown in Data class
         */
        case 'DELETE':
            if (count($path))
                Data::delete($path[0]);

            Request::send('Successfully deleted group.');

        /**
         * Default 404
         */
        default:
            Request::send('Page not found.', 404);

    }

} catch (Exception $e) {

    http_response_code($e->getCode());

    switch ($e->getCode()) {

        case 401:
            if ($_SERVER['REQUEST_METHOD'] == 'POST')
                Request::send($e->getMessage(), $e->getCode());

            else
                require_once(HTML.'/login.php');
                die();

            break;

        default:
            Request::send($e->getMessage(), $e->getCode());

    }

}

// page load layer
require_once(HTML.'/home.php');
die();
