<?php
/**
 * Request class responsible for pulling POST data and sending a response
 * @author Jason Wright <jason@invexi.com>
 * @since 3/7/15
 * @package charon
 */

class Request {

    /**
     * @var stdClass post data
     */
    private $_data;

    /**
     * @var string method
     */
    private $_method;

    /**
     * @var array path
     */
    private $_path;

    /**
     * Singleton factory
     */
    public static function init() {
        return new self();
    }

    /**
     * Constructor
     */
    protected function __construct() {
        $this->_data   = json_decode(file_get_contents('php://input'));
        $this->_method = strtoupper($_SERVER['REQUEST_METHOD']);

        $this->_path = explode('/', $_SERVER['REQUEST_URI']); // break url into fragments
        $this->_path = array_filter($this->_path, 'trim'); // pull out empty values
        $this->_path = array_values($this->_path); // reset array indexes after trimming
    }

    /**
     * Sends an http header, prints the content, and dies
     */
    public static function send($data, $code = 200) {
        $data = is_scalar($data) ? $data : json_encode($data);
        http_response_code($code);
        echo $data;
        die();
    }

    public static function get() {
        return json_decode(file_get_contents('php://input'));
    }

    /**
     * Main route function
     */
    public function route() {

        if (strpos($_SERVER['HTTP_ACCEPT'], 'text/html') !== false) {
            // Route as HTML request
            $this->_route_html();

        } else {
            $this->_route_json();

        }

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
                        $data = Index::read();
                        $data = get_object_vars($data);
                        asort($data);
                        $data = (object)$data;
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
    }

    private function _route_html() {
    }

    private function _route_json() {
    }

}

