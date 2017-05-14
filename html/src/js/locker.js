// check location and redirect
if (location.pathname != '/locker/') location.pathname = '/locker/';

// returns the locker ID from the location hash
function getLockerId() {
    return location.hash.replace(/^[#!\/]*/g, '');
}

// default Locker Object schema
function getBlankLocker() {
    return {
        id: '',
        name: '',
        note: '',
        items: [getBlankItem()],
    }
}

// default Item schema
function getBlankItem() {
    return {
        _id: unique_id(), // unique id to prevent sorting collisions
        icon: 'fa-key',
        title: '',
        url: '',
        user: '',
        pass: '',
        note: '',
    };
}

/**
 * Main Vue component
 */
var lockerApp = new Vue({
    el: '#locker-app',
    data: {

        // display messages
        loader: true,
        success: '',
        error: '',
        objectHash: false,
        object: getBlankLocker(),

        // icon options
        icons: [
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
        ],

        /**
         * Timeouts
         */
        timeouts: {},

        // search query
        query: '',

        /**
         * Index
         * @type {Array}
         */
        index: {},
    },
    created: function() {
        var self = this;
        self.loadIndex();
        self.loadObject();

        self.timeouts.loadIndex = setInterval(self.loadIndex, 3000000); // 5 minutes
    },

    computed: {
        hasChanged: function() {
            return this.objectHash !== md5(json_encode(this.object));
        }
    },

    methods: {

        // clears & resets messages
        clearMessages: function() {
            this.error = this.success = '';
        },

        // Sets the object as a blank object
        resetObject: function() {
            this.object      = getBlankLocker();
            // this.hasChanged = false;
            this.objectHash  = md5(json_encode(this.object));
        },

        /// Adds a blank item to the items array
        addItem: function() {
            if (!this.object.items) this.object.items = [];
            this.object.items.push(getBlankItem());
        },

        // Removes a key row from the group
        removeItem: function(key) {
            this.object.items.splice(key, 1);
        },

        sortItemUpdate: function(event) {
            this.object.items.splice(event.newIndex, 0, this.object.items.splice(event.oldIndex, 1)[0])
        },

        // Function for highlighting an element
        highlight: function(e) {
            // use setTimeout to circumvent safari bug
            setTimeout(function() {
                $(e.target).select();
            }, 10);
        },

        // decrypts, formats, and sets the Locker object
        setObject: function(obj) {
            if (typeof obj === "string")
                obj = json_decode(obj);

            if (obj.items.iv)
                obj.items = AES.decryptToUtf8(obj.items);

            if (typeof obj.items === "string")
                obj.items = json_decode(obj.items);

            // make sure each object has a unique ID before setting
            if (obj.items && obj.items.map) {
                obj.items.map(function(item) {
                    if (item._id === undefined) {
                        delete item.$$hashKey;
                        item._id = unique_id();
                    }
                    item.icon = item.icon && item.icon.length ? item.icon : 'fa-key';
                });
            }

            this.objectHash = md5(json_encode(obj));
            this.object     = obj;
        },

        // Loads the index on to the sidebar
        loadIndex: function() {
            var self = this;
            $.get({
                url: '/locker/_index',
                success: function(result) {
                    self.index = json_decode(result);
                },
                error: function(jqXHR) {
                    console.log(jqXHR);
                    self.error = jqXHR.responseText;
                    if (jqXHR.status === 401)
                        window.logout();
                }
            });
        },

        // Loads a Locker object from the server
        loadObject: function() {
            var self = this;
            self.toggleLoader(true);
            self.clearMessages();

            var lockerId = getLockerId();

            // if we're adding a new group, just
            if (!lockerId.length) {
                self.toggleLoader(false);
                self.resetObject();
                return;
            }

            // send or pull the object
            $.ajax({
                method: 'get',
                url: '/locker/' + lockerId,
                success: function(result) {
                    self.setObject(result);
                    self.toggleLoader(false);

                },
                error: function(jqXHR) {
                    if (code == 401) {
                        location.reload();
                        return;
                    }
                    self.error = jqXHR.responseText;
                    self.toggleLoader(false);
                    self.resetObject();
                }
            });

        },

        // Saves the Locker object
        saveObject: function() {
            var self = this;
            self.toggleLoader(true);
            self.clearMessages();

            var ajaxData = $.extend(true, self.object, {
                items: AES.encrypt(json_encode(self.object.items))
            });

            $.ajax({
                method: 'post',
                url: '/locker/' + self.object.id,
                data: json_encode(self.object),
                success: function(result) {
                    // Set the data into the object
                    self.setObject(result);

                    // set the hash id
                    location.hash = '#/' + self.object.id;

                    self.loadIndex();
                    self.toggleLoader(false);

                    // set success message
                    self.success = 'Successfully saved the object';

                },
                error: function(jqXHR) {
                    if (jqXHR.status == 401) {
                        location.reload();
                        return;
                    }

                    self.error = jqXHR.responseText;
                    self.toggleLoader(false);
                }

            });
        },

        // Permanently deletes the entire Locker object
        deleteObject: function() {
            var self = this;
            self.toggleLoader(true);
            self.clearMessages();

            // send or pull the object
            $.ajax({
                method: 'delete',
                url: '/locker/' + self.object.id,
                success: function(result) {
                    self.success = result;
                    self.resetObject();
                    self.loadIndex();
                    self.toggleLoader(false);
                },
                error: function(jqXHR) {
                    if (jqXHR.status == 401) {
                        location.reload();
                        return;
                    }

                    self.error = data;
                    self.toggleLoader(false);
                }

            });

        },

        // Turns the loader on after a slight delay Or turns it off and clears the timeout
        toggleLoader: function(toggle) {
            var self = this;
            if (toggle) {
                self.timeouts.loader = setTimeout(function() {
                    self.loader = true;
                }, 200);

            } else {
                self.loader = false;
                clearTimeout(self.timeouts.loader);
                window.scrollTo(0, 0);
            }
        },

        // generates a random password for the given item index
        generatePassword: function(index) {
            var self = this;
            if (!self.object.items || !self.object.items[index]) return;

            var chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890!@#$%^&*_-?",
                pass  = "";
            for (var i = 0; i < 16; i++) {
                var key = Math.floor(Math.random() * chars.length);
                pass += chars[key];
            }
            self.object.items[index].pass = pass;
        },


        // Filters the index set according to the query
        search: function(id) {

            // only search if scope query is more than 3
            if (this.query.length < 3) return true;

            var regexp = new RegExp(this.query.replace(' ', '.*'), 'i');

            // first check the group name for a match
            if (this.index[id].name.match(regexp) !== null) return true;

            // if the group name doesn't match, check all meta values
            if (this.index[id].meta !== undefined) {
                for (var i in this.index[id].meta) {
                    if (this.index[id].meta[i].match && this.index[id].meta[i].match(regexp) !== null) {
                        return true;
                    }
                }
            }

            return false;
        },

        // Determines whether the field matches the query string
        fieldMatch: function(value) {
            if (value === undefined || !this.query.length)
                return false;

            var regexp = new RegExp(this.query.replace(' ', '.*'), 'i');
            return value.match(regexp) !== null;
        },

    }
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

/**
 *
 */
$(window).on('hashchange', function() {
    lockerApp.loadObject();
});
