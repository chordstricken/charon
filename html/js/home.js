/**
 * Create angular App
 */
var Charon = angular.module('Charon', ['angular-sortable-view']);

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
            delay: {show: 700, hide: 100},
            trigger: 'hover' ,
            //container: 'body',
        });
        $(this).trigger('mouseover');
    }
});

/**
 * Main content controller
 */
Charon.controller('Home', function($scope, $http, $location, $timeout) {

    // display messages
    $scope.loader  = false;
    $scope.success = '';
    $scope.error   = '';

    /**
     * Timeouts
     */
    $scope.timeouts = {};

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
            items: [],
        };
        $scope.add_item();
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
            _id: unique_id(), // unique id to prevent sorting collisions
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

        }).error(function(data, code) {
            if (code == 401) {
                location.reload();
                return;
            }

            $scope.error = data;

        });
    };

    /**
     * Saves the object
     */
    $scope.save_object = function() {
        $scope.toggle_loader(true);
        $scope.clear_messages();

        // encrypt the object before sending it
        enc_obj = encrypt($scope.object, localStorage.pass);
        enc_obj.items.map(function(item) {
            delete item['$$hashKey'];
        });

        // send or pull the object
        $http.post('/' + $scope.object.id, enc_obj).success(function(data) {
            // Set the data into the object
            $scope.object = decrypt(data, localStorage.pass);

            // set the hash id
            location.hash  = '#/' + $scope.object.id;

            // set success message
            $scope.success = 'Successfully saved the object';

            $scope.index[$scope.object.id] = {
                name: $scope.object.name,
                meta: [],
            }
            $scope.toggle_loader(false);

        }).error(function(data, code) {
            if (code == 401) {
                location.reload();
                return;
            }

            $scope.error = data;
            $scope.toggle_loader(false);

        });
    };

    /**
     * Deletes the object
     */
    $scope.delete_object = function() {
        $scope.toggle_loader(true);
        $scope.clear_messages();

        // send or pull the object
        $http.delete('/' + $scope.object.id).success(function(data) {
            $scope.success = data;
            $scope.reset_object();
            $scope.load_index();
            $scope.toggle_loader(false);

        }).error(function(data, code) {
            if (code == 401) {
                location.reload();
                return;
            }

            $scope.error = data;
            $scope.toggle_loader(false);

        });

    };

    /**
     * Loads an object
     */
    $scope.load_object = function() {
        $scope.toggle_loader(true);
        $scope.clear_messages();

        var path = get_path();

        // if we're adding a new group, just
        if (path == '/') {
            $scope.toggle_loader(false);
            $scope.reset_object();
            return;
        }

        // send or pull the object
        $http.get(path).success(function(data) {
            var dec_obj = decrypt(data, localStorage.pass);

            // make sure each object has a unique ID before setting
            dec_obj.items.map(function(item) {
                if (item._id === undefined) {
                    delete item['$$hashKey'];
                    item._id = unique_id();
                }
            });

            $scope.object = dec_obj;

            $scope.toggle_loader(false);

        }).error(function(data, code) {
            if (code == 401) {
                location.reload();
                return;
            }
            $scope.error = data;
            $scope.toggle_loader(false);
            $scope.reset_object();

        });

    };

    /**
     * Turns the loader on after a slight delay
     * Or turns it off and clears the timeout
     * @param bool toggle
     */
    $scope.toggle_loader = function(toggle) {
        if (toggle) {
            $scope.timeouts.loader = $timeout(function() {
                $scope.loader = true;
            }, 200);

        } else {
            $scope.loader = false;
            $timeout.cancel($scope.timeouts.loader);
            window.scrollTo(0, 0);
        }
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

    /**
     * Filters the index set according to the query
     * @param String id
     * @return Boolean
     */
    $scope.search = function(id) {
        var regexp = new RegExp($scope.query.replace(' ', '.*'), 'i');

        // first check the group name for a match
        if ($scope.index[id].name.match(regexp) !== null) {
            return true;
        }

        // if the group name doesn't match, check all meta values
        if ($scope.index[id].meta !== undefined) {
            for (var i in $scope.index[id].meta) {
                if ($scope.index[id].meta[i].match(regexp) !== null) {
                    return true;
                }
            }
        }

        return false;
    };

    /**
     * Load all page-load items. Used for timeout function
     */
    $scope.load_timeout = function() {
        $scope.load_index();
        // 5 minutes
        $scope.timeouts.index = $timeout($scope.load_timeout, 3600000);
    };

    /* Execute controller functions */
    $scope.load_timeout();

    // bind hash changes to object loading
    // This is called on page load
    $scope.$on('$locationChangeSuccess', function() {
        $scope.load_object();
    });

    // make sure session is intact
    if (!get_cookie('PHPSESSID')) {
        $scope.logout();
    }


});