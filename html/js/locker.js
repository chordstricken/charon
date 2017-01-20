/**
 * Create angular App
 */
var Charon = angular.module('Charon', ['angular-sortable-view']);

function getLockerId() {
    return location.hash.replace(/^[\#\!\/]*/g, '');
}

// // check location and redirect
if (location.pathname != '/locker/') {
    location.pathname = '/locker/';
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
    $scope.loader       = false;
    $scope.success      = '';
    $scope.error        = '';
    $scope.has_changed  = false;
    $scope.object_hash  = false;

    // icon options
    $scope.icons = [
        'fa-key',
        'fa-terminal',
        'fa-database',
        'fa-lock',
        'fa-rocket',
        'fa-truck',

        'fa-envelope-square',
        'fa-book',
        'fa-heartbeat',
        'fa-certificate',
        'fa-expeditedssl',
        'fa-slack',

        'fa-wordpress',
        'fa-linux',
        'fa-apple',
        'fa-android',
        'fa-amazon',
        'fa-windows',

        'fa-instagram',
        'fa-dropbox',
        'fa-google-plus-square',
        'fa-facebook-square',
        'fa-twitter',
        'fa-yelp',

        'fa-ban',
    ];

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
        $scope.has_changed = false;
        $scope.object_hash = md5(json_encode($scope.object));
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
            icon: 'fa-key',
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
        // use setTimeout to circumvent safari bug
        setTimeout(function() {
            $($event.target).select();
        }, 10);
    };

    /**
     * Sets the type on an input
     */
    $scope.set_mask = function($event, mask) {
        $event.target.type = type;
    };

    /**
     * Loads the index on to the sidebar
     */
    $scope.load_index = function() {
        $http.get('/locker/_index').then(function successCallback(result) {
            var resultDecrypted = AES.decrypt(result.data);
            $scope.index = json_decode(AES.decrypt(result.data));

        }, function errorCallback(data, code) {
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
        var enc_obj = AES.encrypt(json_encode($scope.object));

        // send or pull the object
        $http.post('/locker/' + $scope.object.id, enc_obj).then(function successCallback(result) {
            // Set the data into the object
            $scope.object = json_decode(AES.decrypt(result.data));
            console.log('Saved object', $scope.object);

            // set the hash id
            // location.hash  = '#/' + $scope.object.id;

            $scope.load_index();
            $scope.toggle_loader(false);
            $scope.has_changed = false;
            $scope.object_hash = md5(json_encode($scope.object));

            // set success message
            $scope.success = 'Successfully saved the object';

        }, function errorCallback(data, code) {
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
        $http.delete('/locker/' + $scope.object.id).then(function successCallback(result) {
            $scope.success = result.data;
            $scope.reset_object();
            $scope.load_index();
            $scope.toggle_loader(false);

        }, function errorCallback(data, code) {
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

        var lockerId = getLockerId();

        // if we're adding a new group, just
        if (!lockerId.length || lockerId == '/') {
            $scope.toggle_loader(false);
            $scope.reset_object();
            return;
        }

        // send or pull the object
        $http.get('/locker/' + lockerId).then(function successCallback(data) {
            var dec_obj = json_decode(AES.decrypt(data));

            // make sure each object has a unique ID before setting
            if (dec_obj.items && dec_obj.items.map) {
                dec_obj.items.map(function(item) {
                    if (item._id === undefined) {
                        delete item.$$hashKey;
                        item._id = unique_id();
                    }
                    item.icon = item.icon && item.icon.length ? item.icon : 'fa-key';
                });
            }

            $scope.object      = dec_obj;
            $scope.object_hash = md5(json_encode($scope.object));
            $scope.has_changed = false;
            $scope.toggle_loader(false);

        }, function errorCallback(data, code) {
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
        $http.get('/logout').then(function() {
            location.reload();
        }, function() {
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

        // only search if scope query is more than 3
        if ($scope.query.length < 3) {
            return true;
        }

        var regexp = new RegExp($scope.query.replace(' ', '.*'), 'i');

        // first check the group name for a match
        if ($scope.index[id].name.match(regexp) !== null) {
            return true;
        }

        // if the group name doesn't match, check all meta values
        if ($scope.index[id].meta !== undefined) {
            for (var i in $scope.index[id].meta) {
                if ($scope.index[id].meta[i].match && $scope.index[id].meta[i].match(regexp) !== null) {
                    return true;
                }
            }
        }

        return false;
    };

    /**
     * Returns
     * @param value
     * @returns {Array|{index: number, input: string}}
     */
    $scope.field_match = function(value) {
        if (value === undefined || !$scope.query.length)
            return false;

        var regexp = new RegExp($scope.query.replace(' ', '.*'), 'i');
        return value.match(regexp) !== null;
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
    $scope.$on('$locationChangeStart', function(e) {
        if ($scope.has_changed) {
            if (!window.confirm('It looks like you have been editing something. Would you like to leave anyway?')) {
                e.preventDefault();

                // blur the element
                if ("activeElement" in document)
                    document.activeElement.blur();
            }
        }
    });

    // This is called on page load
    $scope.$on('$locationChangeSuccess', function() {
        $scope.load_object();
    });

    // When the object changes, display the notification
    $scope.$watch('object', function() {
        if ($scope.object_hash) {
            var hash = md5(json_encode($scope.object));
            $scope.has_changed = $scope.object_hash !== hash;
        }
    }, true);

    // make sure session is intact
    if (!get_cookie('PHPSESSID')) {
        $scope.logout();
    }

    /**
     * When the user tries to leave the page with unsaved changes, prompt them.
     */
    window.addEventListener('beforeunload', function (e) {
        if ($scope.has_changed) {
            var confirmationMessage = 'It looks like you have been editing something. If you leave before saving, your changes will be lost.';

            (e || window.event).returnValue = confirmationMessage; //Gecko + IE
            return confirmationMessage; //Gecko + Webkit, Safari, Chrome etc.
        }
    });


});

$(document).on('focus', '.password-mask', function() {
    $(this).attr('type', 'text');
});

$(document).on('blur', '.password-mask', function() {
    $(this).attr('type', 'password');
});

/**
 * jQuery based keymap
 */
$(document).on('keyup', function(e) {
    if (e.target.value) {
        return;
    }

    switch (e.keyCode) {

        case 27: // "escape"
            if (document.activeElement)
                document.activeElement.blur();
            break;

        case 191: // "/"
            $('#search').focus();
            break;

    }

});

/**
 * Search keypress event
 */
$(document).on('keyup', '#search', function(e) {
    if (e.keyCode === 13) {
        $('.nav-sidebar a[href]').eq(1).trigger('click');
    }
});