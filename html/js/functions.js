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

/**
 * Encrypts a string
 * @param str
 * @param key
 */
function encrypt(str, key) {
    var iv  = new Date().getTime() + ""; // create iv
    iv  = md5(iv);
    key = md5(key); // hash password
    str = JSON.stringify(str); // encode the param

    var crypt = mcrypt.Encrypt(str, iv, key, 'rijndael-256', 'cbc');

    return {
        crypt: btoa(crypt),
        iv: btoa(iv)
    };
}

/**
 * Decrypts a string
 * @param str
 * @param key
 */
function decrypt(obj, key) {
    obj = typeof(obj) == 'object' ? obj : JSON.parse(obj);
    var dec = mcrypt.Decrypt(atob(obj.crypt), atob(obj.iv), md5(key), 'rijndael-256', 'cbc').trim();
    
    // mcrypt pads the decryption with null bytes. Trim them off
    return JSON.parse(dec.replace(/\x00*$/, ''));
}

/**
 * Generates a unique id
 * @returns {number}
 */
function unique_id() {
    return _uid++;
}
var _uid = new Date().getTime();
