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
</head>
<body>

<div class="text-center">
    <h1>404</h1>
    <?=(isset($data) ? $data : '')?>

</div>

</body>
</html>