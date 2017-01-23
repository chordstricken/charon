

// /**
//  * Create angular App
//  */
// var Charon = angular.module('Charon', []);
//
// /**
//  * Main content controller
//  */
// Charon.controller('Login', function(scope, $http, $location) {
//
//     scope.name = '';
//     scope.pass = '';
//
//     /**
//      * Login Attempt
//      */
//     scope.login_attempt = function() {
//
//         // exchange keys prior to login attempt
//         AES.handshake(function() {
//
//             var data = {
//                 name: scope.name,
//                 pass: scope.pass,
//             };
//
//             scope.error = '';
//
//             // for login, encrypt using session id
//             data = JSON.stringify(data);
//             data = AES.encrypt(data);
//
//             $http.post('/login', data).then(function successCallback(result) {
//                 // console.log(data);
//                 location.reload();
//
//             }, function errorCallback(result, code) {
//                 scope.error = result;
//             });
//
//         });
//     };
//
// });

Vue.component('login-form', {
    template: '#login-form-template',
    data: function() {
        return {
            error: '',
            name: '',
            pass: '',
        }
    },
    created: function() {
        console.log('loaded template');
    },
    methods: {
        loginAttempt: function (e) {
            e.preventDefault();

            var scope   = this,
                ajaxData = {
                    name: scope.name,
                    pass: scope.pass,
                };

            // clear error
            scope.error = '';

            // first, exchange a key
            AES.handshake(function() {
                // for login, encrypt using session id
                ajaxData = AES.encrypt(ajaxData);

                $.post({
                    url: '/login',
                    data: json_encode(ajaxData),
                    dataType: 'json',
                    success: function(result) {
                        location.reload();
                    },
                    error: function(jqXHR, textStatus, errorThrown) {
                        scope.error = jqXHR.responseText;
                    }
                });

            });
        },
    }
});

var vue_container = new Vue({
    el: '#page-container',
});