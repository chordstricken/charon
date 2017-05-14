<?php
$headerOpts = [
    'title' => '404: Not Found',
    'css' => [
        '/src/css/errorpage.css'
    ],
];
?>
<!doctype html>
<html lang="en">
<head>
    <? core\Template::output('header.php', $headerOpts); ?>
    <script src="/dist/js/build.js"></script>
</head>
<body>

<div class="text-center">
    <h1>404</h1>
    <?=(isset($data) ? $data : '')?>
</div>

</body>
<script>
    UserKeychain.setPassword('the sky is falling');
    console.log('HMACKey', UserKeychain.HMACKey);
    console.log('PassHash', UserKeychain.PassHash);
    console.log('ContentKey', UserKeychain.ContentKey);
    console.log('ContentKeyKey', UserKeychain.ContentKeyKey);
</script>
</html>