<?php
/**
 * Tests the encryption method in PHP and JS
 * @author Jason Wright <jason@silvermast.io>
 * @since 1/19/17
 * @package charon
 */
require_once(__DIR__ . '/../core.php');
use core\openssl\AES;

$semanticString      = new core\SemanticString();
$pt                  = $semanticString->getSemanticString(50);
$password            = $semanticString->getSemanticString(5);

$contentKey          = random_bytes(256);
$contentKeyKey       = hex2bin(hash_pbkdf2('sha256', $password, 'Charon.UserKeychain.ContentKeyKey', 15, false));
$contentKeyEncrypted = AES::encrypt($contentKey, $contentKeyKey);
$ptEncrypted         = AES::encrypt($pt, $contentKeyKey);

?>
<!doctype html>
<html>
<head>
<!--    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.0/jquery.min.js"></script>-->
<!--    <script src="/lib/js/cryptojs/rollups/aes.js"></script>-->
<!--    <script src="/lib/js/cryptojs/rollups/md5.js"></script>-->
<!--    <script src="/src/js/build/encryption.js"></script>-->
<!--    <script src="/src/js/build/functions.js"></script>-->
    <script src="/dist/js/build.js"></script>
    <style>
        body {
            width: 100%;
            margin: auto;
            padding: 0 10px;
        }

        li.label {
            font-weight: bold;
            margin-top: 10px;
        }
        .box {
            width: 45%;
            float: left;
            margin: 10px;
            padding: 10px;
            display: inline-block;
            border: 1px solid black;
            word-wrap: break-word;
        }
    </style>
</head>
<body>

    <h1>Testing Encryption</h1>

    <div class="box">
        <h3>PHP</h3>
        <ul id="php">
            <li class="label">contentKeyKey:</li>
            <li><?=json_encode(bin2hex($contentKeyKey))?></li>

            <li class="label">contentKeyEncrypted:</li>
            <li><?=json_encode($contentKeyEncrypted)?></li>

            <li class="label">contentKey:</li>
            <li><?=json_encode(bin2hex($contentKey))?></li>

            <li class="label">Plaintext Encrypted:</li>
            <li><?=json_encode($ptEncrypted)?></li>

            <li class="label">Plaintext:</li>
            <li><?=json_encode($pt)?></li>
        </ul>
    </div>

    <div class="box">
        <h3>JS</h3>
        <ul id="js"></ul>
    </div>

</body>
<script>

    $.fn.appendItem = function(label, value) {
        value = JSON.stringify(value);
        $(this).append('<li class="label">' + label + ':</li><li>' + value + '</li>');
    };


    var $ul = $('#js');

    var password = <?=json_encode($password)?>;
    var contentKeyEncrypted = <?=json_encode($contentKeyEncrypted)?>;
    var ptEncrypted = <?=json_encode($ptEncrypted)?>;

    UserKeychain.setPassword(password);
    UserKeychain.setContentKey(contentKeyEncrypted);

    $ul.appendItem('contentKeyKey', UserKeychain.ContentKeyKey);
    $ul.appendItem('contentKeyEncrypted', contentKeyEncrypted);
    $ul.appendItem('contentKey', UserKeychain.ContentKey);
    $ul.appendItem('Plaintext Encrypted', ptEncrypted);
    $ul.appendItem('Plaintext', AES.decryptToUtf8(ptEncrypted, UserKeychain.getContentKeyKey()));

</script>
</html>