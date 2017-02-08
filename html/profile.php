<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" href="/img/favicon.png" />

    <title><?=APP_NAME?> - Profile</title>

    <link href="/css/build.css" rel="stylesheet">
    <script type="text/x-template" id="tmpl-nav-bar"><?php include(ROOT . '/html/templates/nav-bar.html'); ?></script>
</head>

<body>
    <!-- Fixes the strange chrome/firefox autocomplete spaz bug -->
    <input type="text" name="user" value="" style="display:none;" />
    <input type="password" name="password" value="" style="display:none;" />

    <div id="page-container">

        <div id="locker-app">

            <nav-bar>
<!--                <span class="btn btn-success" v-if="hasChanged" @click="saveObject">Save Pending Changes</span>-->
            </nav-bar>

            <div class="container">

                <div class="text-center" v-show="loader">
                    <img src="/img/loader.svg" width="100%">
                </div>

                <div class="alert alert-success" v-if="success.length">
                    <button type="button" class="close" @click="clearMessages">&times;</button>
                    <span v-text="success"></span>
                </div>
                <div class="alert alert-danger" v-if="error.length">
                    <button type="button" class="close" @click="clearMessages">&times;</button>
                    <span v-text="error"></span>
                </div>

                <h1 class="page-header">My Profile</h1>

                <hr />

                <div class="row">
                    <div class="col-xs-12 col-sm-8 col-md-6 col-lg-4">
                        <div class="form-group">
                            <label>User ID</label>
                            <input type="text" class="form-control" placeholder="kylo.ren@republic.co" v-model="profile.id" readonly>
                        </div>

                        <div class="form-group">
                            <label for="<?=$u=uniqid('label')?>">User Name</label>
                            <input type="text" class="form-control" id="<?=$u?>" placeholder="kylo.ren@republic.co" v-model="profile.name" required>
                        </div>

                        <div class="form-group">
                            <label for="<?=$u=uniqid('label')?>">Email Address</label>
                            <input type="email" class="form-control" id="<?=$u?>" placeholder="kylo.ren@republic.co" v-model="profile.email" required>
                        </div>

                        <div class="form-group" :class="{'has-error': !passwordVerify}">
                            <label for="<?=$u=uniqid('label')?>">Change Password</label>
                            <input type="password" class="form-control" id="<?=$u?>" placeholder="Change Password" v-model="profile.changePass1">
                            <small class="help-block" v-if="!passwordVerify">Password must be at least 12 characters long. Type whatever you'd like, though!</small>
                        </div>
                        <div class="form-group" :class="{'has-error': !passwordsMatch}">
                            <label for="<?=$u=uniqid('label')?>">Verify Password</label>
                            <input type="password" class="form-control" placeholder="Verify Password" v-model="profile.changePass2">
                            <small class="help-block" v-if="!passwordsMatch">The passwords you've entered don't match!</small>
                        </div>

                        <br /><br />
                        <div class="form-group">
                            <button class="btn btn-success btn-lg" @click="saveObject">Save</button>
                        </div>

                    </div>
                </div>
            </div>

        </div>
        <noscript><h1>Javascript is required to use this application.</h1></noscript>

    </div>
</body>

<script src="/js/build.js"></script>
<script src="/js/profile.js"></script>

</html>
