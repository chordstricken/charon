<?php
namespace api;

use \Crypt;
use \Data;
use \Index;
use \Request;
use \User;
use \Exception;
use \stdClass;

/**
 *
 * @author Jason Wright <jason.dee.wright@gmail.com>
 * @since 1/3/17
 * @package charon
 */
class Locker extends Base {

    /**
     * Reads a locker object
     * /locker/:index_id
     */
    public function get() {
        $index_id = $this->path[0];
        if (!$index_id) {
            \Debug::info('Loading home.php');
            require(HTML . '/home.php');
            die();

        } elseif ($index_id === Index::ID) {
            \Debug::info('Loading _index');
            // calling /locker/_index pulls a sorted index array
            $index = Index::read();
            $data  = [];
            foreach ($index as $key => $value) {
                $value->id          = $key;
                $data[$value->name] = $value;
            }
            ksort($data); // sort alphabetically
            $data = array_values($data); // convert from associative array

        } else {
            // load the locker data object
            $data = Data::read($index_id);
        }

        $data = Crypt::enc($data, User::$key);
        Request::send($data);
    }

    /**
     * Saves the Locker object
     * @throws Exception
     */
    public function post() {
        if (!$this->data instanceof stdClass)
            throw new Exception('Invalid object', 400);

        // decrypt the request
        if (!$this->data = Crypt::dec($this->data, User::$key))
            Request::send('Failed to decrypt request.', 500);

        // write the data
        Data::write($this->data);

        // re-encrypt the result before sending it to the user
        $this->data = Crypt::enc($this->data, User::$key);

        // send the response
        Request::send($this->data);
    }

    /**
     * Deletes the Locker object
     */
    public function delete() {
        if (isset($this->path[0]))
            Data::delete($this->path[0]);

        Request::send('Successfully deleted group.');
    }

}