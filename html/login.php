<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title><?=APP_NAME?> - Login</title>

    <link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet" integrity="sha384-wvfXpqpZZVQGK6TAh5PVlGOfQNHSoD2xbE+QkPxCAFlNEevoEH3Sl0sibVcOQVnN" crossorigin="anonymous">
    <link href="/css/charon.min.css" rel="stylesheet">
    <link href="/css/login.css" rel="stylesheet">

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.0/jquery.min.js"></script>
<!--    <script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.5.7/angular.min.js"></script>-->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/vue/2.1.10/vue.min.js"></script>
    <script>localStorage.setItem('server.publicKey', '<?=base64_encode(core\openssl\RSA::getServerKeyPair()->public)?>');</script>
    <script src="/js/charon.min.js"></script>
    <script src="/js/login.min.js"></script>
</head>

<body ng-app="Charon">

<div class="container" ng-controller="Login">
    <script id="LoginForm" type="text/x-template">
        <form class="form-signin" ng-submit="login_attempt()">
            <div class="alert alert-danger" ng-if="error.length">{{error}}</div>

            <h2 class="form-signin-heading">Please sign in</h2>

            <input type="text" class="form-control" placeholder="John Smith" ng-model="name" tabindex="1" required autofocus>
            <br />

            <input type="password" class="form-control" placeholder="somepass123" ng-model="pass" required>

            <br />
            <button class="btn btn-lg btn-primary btn-block" type="submit">Sign in</button>
        </form>
    </script>

    <LoginForm />

    <noscript><h1>Javascript is required to use this application.</noscript>
</div> <!-- /container -->

</body>
</html>
