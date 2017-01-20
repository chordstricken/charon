/**
 * Create angular App
 */
var Charon = angular.module('Charon', []);

/**
 * Main content controller
 */
Charon.controller('Login', function($scope, $http, $location) {

    $scope.name = '';
    $scope.pass = '';

    /**
     * Login Attempt
     */
    $scope.login_attempt = function() {

        // exchange keys prior to login attempt
        AES.handshake(function() {

            var data = {
                name: $scope.name,
                pass: $scope.pass,
            };

            $scope.error = '';

            // for login, encrypt using session id
            data = JSON.stringify(data);
            data = AES.encrypt(data);

            $http.post('/login', data).then(function successCallback(result) {
                // console.log(data);
                location.reload();

            }, function errorCallback(result, code) {
                $scope.error = result;
            });

        });
    };

});