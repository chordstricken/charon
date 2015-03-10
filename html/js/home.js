/**
 * Create angular App
 */
var Charon = angular.module('Charon', []);

function get_path() {
    return location.hash.replace(/^[\#\!]{1,2}/, ''); // get the hash location
}

// check location and redirect
if (location.pathname.length > 1 || location.search.length || location.hash.length < 2) {
    location.href = '/#/';
}

// jQuery bootstrap initiations
$(document).on('mouseover', '[data-toggle=popover]', function() {
    if (!$(this).data('bs.popover')) {
        $(this).popover({
            placement: 'top',
            delay: '200',
            trigger: 'hover' ,
            container: 'body',
        });
        $(this).popover('show');
    }
});

/**
 * Main content controller
 */
Charon.controller('Home', function($scope, $http, $location) {

    // display messages
    $scope.success = '';
    $scope.error   = '';

    // search query
    $scope.query = '';

    /**
     * Index
     * @type {Array}
     */
    $scope.index = {};

    /**
     * Sets the object as a blank object
     */
    $scope.reset_object = function() {
        $scope.object = {
            id: '',
            name: '',
            note: '',
            items: [
                {
                    title: '',
                    url: '',
                    user: '',
                    pass: '',
                    note: '',
                }
            ],
        };
    };

    /**
     * clears the messages
     */
    $scope.clear_messages = function() {
        $scope.error = $scope.success = '';
    };

    /**
     * Adds a blank item to the items array
     */
    $scope.add_item = function() {
        $scope.object.items.push({
            title: '',
            user: '',
            pass: '',
            note: '',
        });
    };

    /**
     * Removes a key row from the group
     * @param key
     */
    $scope.remove_item = function(key) {
        $scope.object.items.splice(key, 1);
    };

    /**
     * Function for highlighting an element
     */
    $scope.highlight = function($event) {
        if ($event.target.type == 'password') {
            $event.target.type = 'text';
        }
        $event.target.select();
    };

    /**
     * Sets the type on an input
     */
    $scope.set_type = function($event, type) {
        if (!$event.target.type) {
            return;
        }
        $event.target.type = type;
    };

    /**
     * Loads the index on to the sidebar
     */
    $scope.load_index = function() {
        $http.get('/_index').success(function(data) {
            var dec = decrypt(data, localStorage.pass);
            $scope.index = dec;
        });
    };

    /**
     * Saves the object
     */
    $scope.save_object = function() {
        $scope.clear_messages();

        // encrypt the object before sending it
        var enc_obj = encrypt($scope.object, localStorage.pass);

        // send or pull the object
        $http.post('/' + $scope.object.id, enc_obj).success(function(data) {
            // Set the data into the object
            $scope.object = decrypt(data, localStorage.pass);

            // set the hash id
            location.hash  = '#/' + $scope.object.id;

            // set success message
            $scope.success = 'Successfully saved the object';

            $scope.index[$scope.object.id] = $scope.object.name;

        }).error(function(data, code) {
            if (code == 401) {
                location.reload();
                return;
            }

            $scope.error = data;

        });
    };

    /**
     * Deletes the object
     */
    $scope.delete_object = function() {

        $scope.clear_messages();

        // send or pull the object
        $http.delete('/' + $scope.object.id).success(function(data) {
            $scope.success = data;
            $scope.reset_object();
            $scope.load_index();

        }).error(function(data, code) {
            if (code == 401) {
                location.reload();
                return;
            }

            $scope.error = data;

        });

    };

    /**
     * Loads an object
     */
    $scope.load_object = function() {
        var path = get_path();

        $scope.clear_messages();

        // if we're adding a new group, just
        if (path == '/') {
            $scope.reset_object();
            return;
        }

        // send or pull the object
        $http.get(path).success(function(data) {
            $scope.object = decrypt(data, localStorage.pass);

        }).error(function(data, code) {
            if (code == 401) {
                location.reload();
                return;
            }
            $scope.error = data;
            $scope.reset_object();

        });

    };

    $scope.logout = function() {
        $http.get('/logout').success(function() {
            location.reload();
        }).error(function() {
            location.reload();
        });
    };

    /**
     * Regenerates a password
     * @param Number index
     */
    $scope.generate_password = function(index) {
        var chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890!@#$%^&*_-?",
            pass  = "";
        for (var i = 0; i < 16; i++) {
            var key = Math.floor(Math.random() * chars.length);
            pass += chars[key];
        }

        $scope.object.items[index].pass = pass;
    };


    /* Execute controller functions */


    $scope.load_index(); // pull the index
    $scope.load_object();

    // bind hash changes to object loading
    $scope.$on('$locationChangeSuccess', function() {
        $scope.load_object();
    });

    // make sure session is intact
    if (!get_cookie('PHPSESSID')) {
        $scope.logout();
    }


});