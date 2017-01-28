<?php
/**
 * Tests the encryption method in PHP and JS
 * @author Jason Wright <jason@silvermast.io>
 * @since 1/19/17
 * @package charon
 */
require_once(__DIR__ . '/../core.php');
use core\openssl\AES;

$plaintext = 'I fear nothing. I am free.';

/**
 * Stackoverflow test
 */
//$key    = hash_pbkdf2("sha256", AES::getRandomKey(), uniqid(), 1000, 32, true);
////$key    = AES::getRandomKey();
//$iv     = openssl_random_pseudo_bytes(openssl_cipher_iv_length("aes-256-cbc"));
//$cipherText = openssl_encrypt($plaintext, "aes-256-cbc", $key, 0, $iv);
//$cipher = base64_encode($cipherText . ":" . bin2hex($iv));
//
//
//$key = base64_encode($key);

/**
 * My test
 */
$key       = AES::getRandomKey();
$cipher    = AES::encrypt($plaintext, $key);

?>
<!doctype html>
<html>
<head>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.0/jquery.min.js"></script>
    <script src="/js/cryptojs/rollups/aes.js"></script>
<!--    <script src="/js/cryptojs/components/mode-ctr-min.js"></script>-->
    <script src="/js/cryptojs/rollups/md5.js"></script>
<!--    <script src="/js/cryptojs/components/hmac.js"></script>-->
<!--    <script src="/js/cryptojs/components/sha256.js"></script>-->
<!--    <script src="/js/cryptojs/components/pbkdf2.js"></script>-->
    <script src="/js/jsencrypt.js"></script>
    <script src="/js/functions.js"></script>
    <style>
        li.label { font-weight: bold; margin-top: 10px; }
    </style>
</head>
<body>
<div style="width: 1000px; margin:auto;">

    <h1>Testing Encryption</h1>
    <ul>
        <li class="label">Plaintext:</li>
        <li><?=var_dump($plaintext)?></li>

        <li class="label">Key (base64):</li>
        <li><?=var_dump($key)?></li>

        <li class="label">PHP Cipher:</li>
        <li><?=var_dump($cipher)?></li>

        <li class="label">PHP Decrypted:</li>
        <li><?=AES::decrypt($cipher, $key)?></li>

    </ul>

</div>
</body>
<script>
    $(function() {
// Stackoverflow test
//        var crypt = <?//=$cipher?>//;
//
////        var key256Bits  = CryptoJS.PBKDF2("0000", "secret", { keySize: 256/32, iterations: 1000, hasher: CryptoJS.algo.SHA256 });
//        var key256Bits = CryptoJS.enc.Base64.parse('<?//=$key?>//');
//        console.log(key256Bits);
////        var key256Bits  = '<?////=$key?>////';
//
////        var rawData = atob(crypt.cipher);
////        var rawPieces = rawData.split(":");
//
//        var crypttext = crypt.cipher;
//        var iv = CryptoJS.enc.Base64.parse(crypt.iv);
//
//        var cipherParams = CryptoJS.lib.CipherParams.create({ciphertext: CryptoJS.enc.Base64.parse(crypttext)});
//
//        var plaintextArray = CryptoJS.AES.decrypt(
//            cipherParams,
//            key256Bits,
//            { iv: iv }
//        );
//
//        var plaintext = plaintextArray.toString(CryptoJS.enc.Utf8);
//        console.log(plaintext);
//
//        $('ul').append('<li class="label">JS Result:</li><li>string (' + plaintext.length + ') "' + plaintext + '"</li>');

// My test
        AES.setKey('<?=$key?>');
        var cipher    = '<?=$cipher?>',
            plaintext = AES.decrypt(cipher);

        $('ul').append('<li class="label">JS Decrypted:</li><li>string (' + plaintext.length + ') "' + plaintext + '"</li>');

        var jsCipher = JSON.stringify(AES.encrypt(plaintext));
        $('ul').append('<li class="label">JS Cipher:</li><li>string (' + jsCipher.length + ') "' + jsCipher + '"</li>');

        var plaintext2 = AES.decrypt(jsCipher);
        $('ul').append('<li class="label">JS Decrypted:</li><li>string (' + plaintext2.length + ') "' + plaintext2 + '"</li>');
    });
</script>
</html>