<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title><?=APP_NAME?> - Login</title>

    <link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet" integrity="sha384-wvfXpqpZZVQGK6TAh5PVlGOfQNHSoD2xbE+QkPxCAFlNEevoEH3Sl0sibVcOQVnN" crossorigin="anonymous">
    <link href="/css/charon.min.css" rel="stylesheet">
    <link href="/css/login.css" rel="stylesheet">
</head>

<!-- App body -->
<body>

    <div id="page-container" class="container">

        <!-- Login Form template -->
        <script type="text/x-template" id="login-form-template">
            <form class="form-signin" @submit="loginAttempt">
                <div class="alert alert-danger" v-if="error.length" v-html="error"></div>

                <h2 class="form-signin-heading">Please sign in</h2>

                <input type="text" class="form-control" placeholder="John Smith" v-model="name" tabindex="1" required autofocus>
                <br />

                <input type="password" class="form-control" placeholder="somepass123" v-model="pass" required>
                <br />

                <button class="btn btn-lg btn-primary btn-block" type="submit">Sign in</button>
            </form>
        </script>
        <login-form></login-form>

        <noscript><h1>Javascript is required to use this application.</noscript>

    </div> <!-- /container -->
</body>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.0/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/vue/2.1.10/vue.js"></script>
<script>localStorage.setItem('server.publicKey', '<?=base64_encode(core\openssl\RSA::getServerKeyPair()->public)?>');</script>
<script src="/js/charon.min.js"></script>
<script src="/js/login.js"></script>

</html>
