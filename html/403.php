<?php
$headerOpts = [
    'title' => '403: Forbidden',
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
        <h1>403</h1>
        <?=(isset($data) ? $data : '')?>

    </div>

</body>
</html>