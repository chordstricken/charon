<?php
/**
 * Index class responsible storing a list of all items
 * @author Jason Wright <jason@invexi.com>
 * @since 2/18/15
 * @package charon
 */

class Crypt {

    /**
     * Default Encryption key
     */
    const KEY = 'There Chairon stands, who rules the dreary coast - | A sordid god: down from his hairy chin | A length of beard descends, uncombed, unclean; | His eyes, like hollow furnaces on fire; | A girdle, foul with grease, binds his obscene attire.';

    /**
     * Default Encryption Mode
     */
    const MODE = MCRYPT_MODE_CBC;

    /**
     * Default Encryption Type
     */
    const TYPE = MCRYPT_RIJNDAEL_256;

    /**
     * @param $str
     * @param string $key
     * @return string (json)
     */
    public static function enc($encrypt, $key = self::KEY) {

        // set the params
        $key     = self::_key($key);
        $encrypt = json_encode($encrypt);
        $iv      = self::_iv();

        $crypt = mcrypt_encrypt(self::TYPE, $key, $encrypt, self::MODE, $iv);
        
        return json_encode([
            'crypt' => base64_encode($crypt),
            'iv'    => base64_encode($iv),
        ]);

    }

    /**
     * @param $json (json)
     * @param string $key
     * @return string (decrypted)
     */
    public static function dec($obj, $key = self::KEY) {

        // Verify item is a json object
        if (is_string($obj) && !$obj = json_decode($obj))
            throw new Exception(__METHOD__ . ' Failed to decode JSON', 500);

        // verify crypt is set
        if (!isset($obj->crypt))
            throw new Exception(__METHOD__ . ' Decryption object missing crypt param', 500);

        // verify iv is set
        if (!isset($obj->iv))
            throw new Exception(__METHOD__ . ' Decryption object missing iv param', 500);

        $key        = self::_key($key);
        $obj->crypt = base64_decode($obj->crypt);
        $obj->iv    = base64_decode($obj->iv);

        // decrypt
        $decrypted = trim(mcrypt_decrypt(self::TYPE, $key, $obj->crypt, self::MODE, $obj->iv));
        
        return json_decode($decrypted);

    }

    /**
     * Generates the Initialization Vector
     */
    private static function _iv() {
        return mcrypt_create_iv(mcrypt_get_iv_size(self::TYPE, self::MODE), MCRYPT_DEV_URANDOM);
    }

    /**
     * Hashes a string
     * @param $str
     * @return string
     */
    private static function _key($str) {
        return md5($str);
    }

}

