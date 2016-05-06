<?php
/**
 * Data class responsible for pulling a /data/json object
 * @author Jason Wright <jason@invexi.com>
 * @since 2/18/15
 * @package charon
 */

class Data {

    /**
     * Reads an object
     * @param string $name
     * @return stdClass $object
     * @throws Exception
     */
    public static function read($name) {
        if ($name[0] == '.' || $name[0] == '_') {
            throw new Exception("Invalid group name.", 404);
        }
        return File::read($name);
    }

    /**
     * Saves an object into the database
     * @param stdClass $object
     * @return stdClass $object
     * @throws Exception (406)
     */
    public static function write($object) {
        if (empty($object->name)) {
            throw new Exception('Please specify a name', 406);
        }

        if (empty($object->id)) {
            $object->id = hash('sha256', uniqid('charon', true));
        }

        File::write($object->id, $object);

        $meta = array();

        // iterate over the items and build an array of meta values for searching.
        // We'll use title, url, and user
        foreach ($object->items as $item) {

            if (isset($item->title) && trim($item->title))
                $meta[$item->title] = 1;

            if (isset($item->url) && trim($item->url))
                $meta[$item->url] = 1;

            if (isset($item->user) && trim($item->user))
                $meta[$item->user] = 1;

        }

        if (isset($object->note) && trim($object->note))
            $meta[$object->note] = 1;

        // add the item to the new index
        Index::add($object->id, $object->name, array_keys($meta));

        return $object;
    }

    /**
     * Deletes an object
     * @param $name
     */
    public static function delete($name) {
        Index::remove($name);
        File::delete($name);
    }

}

