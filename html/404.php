<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" href="/img/favicon.png" />

    <title>Charon - 404: Not Found</title>

    <link href="/src/css/build.css" rel="stylesheet">
    <link href="/src/css/errorpage.css" rel="stylesheet">
</head>
<body>

    <div class="text-center">
        <h1>404</h1>
        <?=(isset($data) ? $data : '')?>
    </div>

</body>
</html>