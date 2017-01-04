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
     * Login Attepmt
     */
    $scope.login_attempt = function() {
        var data = {
            name: $scope.name,
            pass: $scope.pass,
            key: sha256($scope.pass),
        };

        // store the sha256 passphrase for encryption purposes
        localStorage.setItem('key', data.key);

        $scope.error = '';

        // for login, encrypt using session id
        var enc_data = encrypt(data, get_cookie('PHPSESSID'));

        $http.post('/login', enc_data).success(function(data) {
            location.reload();

        }).error(function(result, code) {
            $scope.error = result;
        });
    };

});