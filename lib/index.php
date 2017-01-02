<?php
/**
 * Index class responsible storing a list of all items
 * @author Jason Wright <jason.dee.wright@gmail.com>
 * @since 2/18/15
 * @package charon
 */

class Index {

    /**
     * @var string filename
     */
    const ID = '_index';

    /**
     * @var stdClass index
     */
    private static $_index;

    /**
     * Retrieves the Index
     * @return stdClass
     */
    public static function read() {
        if (!self::$_index instanceof stdClass) {
            try {
                self::$_index = File::read(self::ID);

            } catch (Exception $e) {
                self::$_index = new stdClass();
                File::write(self::ID, self::$_index);

            }
        }
        return self::$_index;
    }

    /**
     * Saves the Index
     * @return stdClass $object
     * @throws Exception
     */
    public static function write() {
        if (!self::$_index instanceof stdClass) {
            return;
        }

        File::write(self::ID, self::$_index);
    }

    /**
     * Adds an item to the index
     * @param $id
     * @param $name
     */
    public static function add($id, $name, array $meta) {
        self::read();
        self::$_index->{$id} = [
            'name' => $name,
            'meta' => $meta,
        ];
        self::write();
    }

    /**
     * Removes an item from the index
     * @param $id
     */
    public static function remove($id) {
        self::read();
        unset(self::$_index->{$id});
        self::write();
    }

    /**
     * @param string $query
     * @return stdClass index
     */
    public static function search($query) {
        return stdClass();
    }

}

