<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Charon - Login</title>

    <link href="/css/rollup.css.php" rel="stylesheet">
    <link href="/css/login.css" rel="stylesheet">
    <script type="text/javascript" src="/js/rollup.js.php"></script>
    <script type="text/javascript" src="/js/login.js"></script>
</head>

<body ng-app="Charon">

<div class="container" ng-controller="Login">

    <form class="form-signin" ng-submit="login_attempt()">
        <div class="alert alert-danger" ng-if="error.length">{{error}}</div>

        <h2 class="form-signin-heading">Please sign in</h2>

        <input type="text" class="form-control" placeholder="John Smith" ng-model="name" tabindex="1" required autofocus>
        <br />
        
        <input type="password" class="form-control" placeholder="somepass123" ng-model="pass" required>

        <br />
        <button class="btn btn-lg btn-primary btn-block" type="submit">Sign in</button>
    </form>

    <div class="text-right">
        <small>&copy <?=date('Y')?> Charon v<?=CHARON_VERSION?></small>
    </div>

</div> <!-- /container -->

</body>
</html>

