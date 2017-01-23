/**
 * Stores a cookie
 * @param string key
 * @param mixed val
 * @param int days (ttl)
 */
function set_cookie(key, val, days)
{
    var exdate = new Date();
    days = days !== undefined ? days : 7;
    exdate.setDate(exdate.getDate() + days);
    document.cookie = key + "=" + encodeURIComponent(val) + "; expires=" + exdate.toUTCString() + ";path=/";
}

/**
 * Retrieves a cookie
 * @param  string key_param
 * @return mixed
 */
function get_cookie(key)
{
    var cookies = document.cookie.split(';'), i;
    for (i in cookies) {
        var split = cookies[i].split('=');
        if (split[0].trim() == key) {
            return decodeURIComponent(split[1]);
        }
    }
    return null;
}

function md5(string) {
    return CryptoJS.MD5(string).toString();
}

/**
 * Returns an object or the param as it was passed in
 * @param param
 * @returns {*}
 */
function json_decode(param) {
    if (typeof(param) == 'object')         return param;
    if (!param.length || param.length < 2) return param;

    try {
        return JSON.parse(param);
    } catch (e) {
        return param;
    }
}

function json_encode(param) {
    return JSON.stringify(param);
}

/**
 * Generates a unique id
 * @returns {number}
 */
function unique_id() {
    return _uid++;
}
var _uid = new Date().getTime();


/**
 * AES class for encrypting and decrypting data.
 * Keys are generated on the server and stored in the browser
 * @type {AES}
 */
var AES = new function() {

    var keyObj = json_decode(localStorage.getItem('encryptionKeyObj'));

    /**
     * Uses RSA to exchange private encryption keys with the server
     * @param success
     */
    this.handshake = function(success) {
        if (keyObj && keyObj.key &&  keyObj.sessionId && keyObj.sessionId == get_cookie('PHPSESSID')) {
            if (typeof(success) === 'function') success();

        } else {
            $.post('/handshake', RSA.encryptForServer(RSA.clientPublicKey), function(result) {
                try {
                    result = RSA.decryptFromServer(result); // AES key
                    console.log(result);

                    keyObj = {
                        key: result,
                        sessionId: get_cookie('PHPSESSID'),
                    };

                    // store the session id along with the key to ensure that it matches with the server
                    localStorage.setItem('encryptionKeyObj', json_encode(keyObj));

                    if (typeof(success) === 'function') success();

                }
                catch (e) {
                    console.log('Error:', e);
                }
            });
        }
    };

    /**
     * Encrypts the data parameter and returns a hex (lowercase) string
     * @param plaintext
     */
    this.encrypt = function(plaintext) {
        if (typeof(plaintext) == 'object')
            plaintext = json_encode(plaintext);

        var key    = CryptoJS.enc.Base64.parse(keyObj.key);
        var result = CryptoJS.AES.encrypt(plaintext, key, {iv: CryptoJS.lib.WordArray.random(16)});

        return {
            cipher: result.ciphertext.toString(CryptoJS.enc.Base64),
            iv: result.iv.toString(CryptoJS.enc.Base64)
        };

    };

    /**
     * Accepts a byte array or hex string (lowercase)
     * @param encObj JSON or object
     */
    this.decrypt = function(encObj) {
        // convert encObj hex string into byte array
        if (typeof(encObj) === 'string')
            encObj = json_decode(encObj);

        if (!encObj.iv || !encObj.cipher)
            return encObj;

        var key          = CryptoJS.enc.Base64.parse(keyObj.key);
        var cipherParams = CryptoJS.lib.CipherParams.create({ciphertext: CryptoJS.enc.Base64.parse(encObj.cipher)});
        var opts         = {iv: CryptoJS.enc.Base64.parse(encObj.iv)};

        try {
            var result = CryptoJS.AES.decrypt(cipherParams, key, opts).toString(CryptoJS.enc.Utf8);

            // decrypting results in double quote (") padding. Remove them.
            if (result[0] == '"' && result.substr(-1) == '"')
                result = result.replace(/^"|"$/g, '');

            return result;

        } catch (e) {
            if (!result) console.log('Decryption failed.');
            return '';
        }

    };

};

/**
 * RSA Class for encrypting and decrypting data
 * For encrypting and decrypting server requests and responses,
 * we will use the server's public key and our own private key.
 *
 * JSEncrypt
 */
var RSA = new function() {
    this.serverCrypt = new JSEncrypt({default_key_size: 4096});
    this.clientCrypt = new JSEncrypt({default_key_size: 1024});

    // save/load client keys in localStorage
    var publicKey = localStorage.getItem('client.publicKey') || this.clientCrypt.getPublicKey();
    var privateKey = localStorage.getItem('client.privateKey') || this.clientCrypt.getPrivateKey();
    this.clientCrypt.setPublicKey(publicKey);
    this.clientCrypt.setPrivateKey(privateKey);
    localStorage.setItem('client.publicKey', publicKey);
    localStorage.setItem('client.privateKey', privateKey);

    // set public properties
    this.serverPublicKey = atob(localStorage.getItem('server.publicKey'));
    this.clientPublicKey = this.clientCrypt.getPublicKey(); // pull out the client public key
    this.serverCrypt.setPublicKey(this.serverPublicKey);

    /**
     * encrypts with the public key
     * @param data (String)
     * @returns {string} (base64 encoded hex)
     */
    this.encryptForServer = function(data) {
        return btoa(this.serverCrypt.getKey().encrypt(data));
    };

    /**
     * decrypts with the private key
     * @param data (base64 encoded hex)
     * @returns String|null
     */
    this.decryptFromServer = function(data) {
        return this.clientCrypt.getKey().decrypt(atob(data));
    };

};