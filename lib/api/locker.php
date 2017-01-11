<?php
namespace api;

use core;
use models;
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
        $index_id = $this->path[0] ?? false;
        if (!$index_id) {
            require(HTML . '/locker.php');
            die();

        } elseif ($index_id === '_index') {
            // calling /locker/_index pulls a sorted index array
            $data = $this->_get_index();

        } else {
            // load the locker data object
            $data = models\Locker::findOne(['id' => $index_id]);
        }

        $data = core\Crypt::enc($data, models\User::getKey());
        $this->send($data);
    }

    /**
     * Saves the Locker object
     * @throws Exception
     */
    public function post() {
        if (!$this->data instanceof stdClass)
            throw new Exception('Invalid Request Object', 400);

        // decrypt the request
        if (!$this->data = core\Crypt::dec($this->data, models\User::getKey()))
            throw new Exception('Failed to decrypt request.', 500);

        $locker = models\Locker::new($this->data)->validate()->save();

        // re-encrypt the result before sending it to the user
        $this->data = core\Crypt::enc($locker, models\User::getKey());

        // send the response
        $this->send($this->data);
    }

    /**
     * Deletes the Locker object
     */
    public function delete() {
        if (isset($this->path[0]))
            models\Locker::new(['id' => $this->path[0]])->delete();

        $this->send('Successfully deleted Locker.');
    }

    /**
     * @return array
     */
    private function _get_index(): array {
        $index = [];

        // @todo: create more memory-friendly implementation
        $lockers = models\Locker::findMulti([]);
        foreach ($lockers as $locker) {
            $meta = [];

            foreach ($locker->items as $item) {
                if (isset($item->title) && $item->title = trim($item->title)) $meta[$item->title] = 1;
                if (isset($item->url) && $item->url = trim($item->url))       $meta[$item->url]   = 1;
                if (isset($item->user) && $item->user = trim($item->user))    $meta[$item->user]  = 1;
            }

            if ($locker->note = trim($locker->note))
                $meta[$locker->note] = 1;

            $index[$locker->name] = [
                'id'   => $locker->id,
                'name' => $locker->name,
                'meta' => array_keys($meta),
            ];
        }

        ksort($index);
        return array_values($index);
    }
}