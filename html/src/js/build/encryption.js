
var HMAC = new function() {

    this.getKey = function() {
        return UserKeychain.getHMACKey();
    };

    /**
     * Returns an Hmac tag
     * @param data (base64 string)
     * @return tag (base64 string)
     */
    this.getTag = function(data) {
        if (!data || !data.length) {
            console.log('Cannot get tag for ', data);
            return false;
        }

        var dataObj = CryptoJS.enc.Base64.parse(data);
        return CryptoJS.HmacSHA256(dataObj, this.getKey()).toString(CryptoJS.enc.Base64);
    };

    /**
     * Verifies a tag against a data/key pair
     * @param data
     * @param tag (base64)
     * @returns {boolean}
     */
    this.verifyTag = function(data, tag) {
        return this.getTag(data) === tag;
    };
};

/**
 * AES class for encrypting and decrypting data.
 * Keys are generated on the server and stored in the browser
 * @type {AES}
 */
var AES = new function() {

    function getKey() {
        return UserKeychain.getContentKey();
    }

    /**
     * Encrypts the data parameter and returns a hex (lowercase) string
     * @param plaintext
     * @param keyObj optional
     */
    this.encrypt = function(plaintext, keyObj) {

        if (plaintext instanceof Object)
            plaintext = json_encode(plaintext);

        var result = CryptoJS.AES.encrypt(plaintext, keyObj || getKey(), {iv: CryptoJS.lib.WordArray.random(16)});

        var retObj = {
            cipher: result.ciphertext.toString(CryptoJS.enc.Hex),
            iv: result.iv.toString(CryptoJS.enc.Hex)
        };

        // retObj.tag = HMAC.getTag(retObj.cipher); // @todo implement HMAC
        return retObj;
    };

    this.decrypt = function(encObj, keyObj) {
        // convert encObj hex string into byte array

        if (!encObj instanceof Object)
            encObj = json_decode(encObj);

        if (!encObj.iv || !encObj.cipher)
            return encObj;

        /** @todo: HMAC tag verification */
        // if (encObj.tag && !HMAC.verifyTag(encObj.cipher, encObj.tag)) {
        //     console.log('Ciphertext Verification failed.');
        //     return '';
        // }

        var cipherParams = CryptoJS.lib.CipherParams.create({ciphertext: CryptoJS.enc.Hex.parse(encObj.cipher)});
        var opts         = {iv: CryptoJS.enc.Hex.parse(encObj.iv)};

        try {
            return CryptoJS.AES.decrypt(cipherParams, keyObj || getKey(), opts);

        } catch (e) {
            if (!result) console.log('Decryption failed.');
            return '';
        }

    };

    /**
     * Accepts a byte array or hex string (lowercase)
     * @param encObj JSON or object
     * @param keyObj optional
     */
    this.decryptToUtf8 = function(encObj, keyObj) {
        return this.decrypt(encObj, keyObj).toString(CryptoJS.enc.Utf8);
    };

};

/**
 * Keychain object used for expanding user keys
 */
var UserKeychain = new function() {

    var self = this;

    // All keys stored into sessionStorage as base64 values
    self.HMACKey       = localStorage.getItem('UserKeychain.HMACKey'); // used for signing requests
    self.PassHash      = localStorage.getItem('UserKeychain.PassHash'); // hash of the password
    self.ContentKey    = localStorage.getItem('UserKeychain.ContentKey'); // used for decrypting Account data
    self.ContentKeyKey = localStorage.getItem('UserKeychain.ContentKeyKey'); // used for decrypting the contentKey

    // Returns a CryptoJS instance of the provided keyname
    self.getKey = function(keyname) {
        var keyObjName = '_' + keyname;
        if (self[keyObjName]) return self[keyObjName];
        self[keyObjName] = CryptoJS.enc.Hex.parse(self[keyname]);
        return self[keyObjName];
    };

    // Expands a password into a passkey with the provided CryptoJS PBKDF2 options
    self.expandKey = function(keyname, passObj, opts) {
        self['_' + keyname] = CryptoJS.PBKDF2(passObj, 'Charon.UserKeychain.' + keyname, opts);
        self[keyname] = self['_' + keyname].toString(CryptoJS.enc.Hex);
        localStorage.setItem('UserKeychain.' + keyname, self[keyname])
    };

    self.getHMACKey       = function() { return self.getKey('HMACKey'); };
    self.getPassHash      = function() { return self.getKey('PassHash'); };
    self.getContentKey    = function() { return self.getKey('ContentKey'); };
    self.getContentKeyKey = function() { return self.getKey('ContentKeyKey'); };

    // expands a password into various keys
    self.setPassword = function(pass) {
        var passObj = CryptoJS.enc.Utf8.parse(pass);

        self.expandKey('HMACKey', passObj, {
            iterations: 10,
            hasher: CryptoJS.algo.SHA256,
            keySize: 256/32,
        });

        self.expandKey('PassHash', passObj, {
            iterations: 20,
            hasher: CryptoJS.algo.SHA256,
            keySize: 256/32,
        });

        self.expandKey('ContentKeyKey', passObj, {
            iterations: 15,
            hasher: CryptoJS.algo.SHA256,
            keySize: 256/32,
        });
    };

    // Decrypts the content key and sets it
    self.setContentKey = function(contentKeyEncrypted) {
        self._ContentKey = AES.decrypt(contentKeyEncrypted, self.getContentKeyKey());
        self.ContentKey  = self._ContentKey.toString(CryptoJS.enc.Hex);
        localStorage.setItem('UserKeychain.ContentKey', self.ContentKey);
    }

};