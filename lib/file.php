<?php
/**
 * File class responsible for pulling and storing files
 * @author Jason Wright <jason.dee.wright@gmail.com>
 * @since 2/18/15
 * @package charon
 */

class File {

    /**
     * Retrieves an object
     * @param string $name
     * @return stdClass $object
     * @throws Exception (404, 400)
     */
    public static function read($name) {
        $path = self::_get_path($name);

        if (!file_exists($path)) {
            throw new Exception(__METHOD__ . " $name does not exist", 404);
        }

        if (!$raw_data = file_get_contents($path)) {
            throw new Exception(__METHOD__ . " Failed to read data for $name", 400);
        }

        if (!$object = self::_decode($raw_data)) {
            throw new Exception(__METHOD__ . " Failed to decode data for $name", 400);
        }

        return $object;
    }

    /**
     * Saves an object
     * @param string $name
     * @param mixed $object
     * @throws Exception (400)
     */
    public static function write($name, $object) {
        $path = self::_get_path($name);

        if (!file_exists($path)) {
            self::create($name);
        }

        if (file_put_contents($path, self::_encode($object)) === false) {
            throw new Exception(__METHOD__ . ' Failed to write the object', 400);
        }

    }

    /**
     * deletes an object
     * @param string $name
     * @throws Exception (400)
     */
    public static function delete($name) {
        if (empty($name) || $name[0] == '_') {
            throw new Exception(__METHOD__ . " Deleting '$name' is not allowed");
        }

        $path = self::_get_path($name);

        if (!unlink($path)) {
            throw new Exception(__METHOD__ . ' Failed to delete the object', 400);
        }
    }

    /**
     * Creates the file with proper permissions
     * @param string $name
     */
    public static function create($name) {
        $path = self::_get_path($name);
        touch($path);
        chmod($path, 0644);
    }

    /**
     * Gets the file path for an item
     * @param string $name
     * @return string path
     */
    private static function _get_path($name) {
        return ROOT."/data/$name";
    }

    /**
     * Encodes an item
     * @param string $input
     * @return string $input
     * @throws Exception
     */
    private static function _encode($input) {
        // convert to json
        if (!$input = json_encode($input))
            throw new Exception(__METHOD__ . ' Failed to encode data', 500);

        // encrypt, turning it into an {iv, cipher} object
        if (!$input = Crypt::enc($input))
            throw new Exception(__METHOD__ . ' Failed to encrypt data', 500);

        // then compress
        if (!$input = gzcompress($input))
            throw new Exception(__METHOD__ . ' Failed to compress data', 500);

        return $input;

    }

    /**
     * Decodes an item
     * @param string $input
     * @return string $input
     * @throws Exception
     */
    private static function _decode($input) {
        // If the string is empty, just return it
        if (mb_strlen($input) === 0)
            return $input;

        // First uncompress
        if (!$input = gzuncompress($input))
            throw new Exception(__METHOD__ . ' Failed to uncompress data', 500);

        // decrypt
        if (!$input = Crypt::dec($input))
            throw new Exception(__METHOD__ . ' Failed to decrypt data', 500);

        // Then decode json
        if (!$input = json_decode($input))
            throw new Exception(__METHOD__ . ' Failed to decode data', 500);

        return $input;

    }

}

