
/**
 * Main Vue component
 */
var profileApp = new Vue({
    el: '#locker-app',
    data: {

        // display messages
        loader: true,
        success: '',
        error: '',

        changePass1: '',
        changePass2: '',

        profile: {},

        timeouts: {},
    },
    created: function() {
        var scope = this;
        $.get({
            url: '/profile',
            success: function(result) {
                scope.profile = $.extend(scope.profile, json_decode(result));
                scope.toggleLoader(false);
            }
        });
    },

    computed: {
        passwordChange: function() {
            return this.changePass1.length > 0 && this.changePass2.length > 0;
        },
        passwordVerify: function() {
            return this.changePass1.length === 0 || this.changePass1.length > 12;
        },
        passwordsMatch: function() {
            return this.changePass1.length === 0 || this.changePass1 === this.changePass2;
        }
    },

    methods: {

        // clears & resets messages
        clearMessages: function() {
            this.error = this.success = '';
        },

        saveObject: function() {
            var scope = this;

            if (scope.passwordChange) {
                // passwords must match
                if (!scope.passwordsMatch) {
                    scope.error = 'Passwords do not match.';
                    return;
                }

                UserKeychain.setPassword(scope.changePass1);
                scope.profile.passhash            = UserKeychain.PassHash;
                scope.profile.contentKeyEncrypted = UserKeychain.getContentKeyEncrypted();
            }

            scope.clearMessages();
            scope.toggleLoader(true);

            $.post({
                url: '/profile',
                data: json_encode(scope.profile),
                success: function(result) {

                    scope.success     = "Successfully updated your profile!";
                    scope.profile     = $.extend(scope.profile, json_decode(result));
                    scope.changePass1 = '';
                    scope.changePass2 = '';
                    scope.toggleLoader(false);

                    UserKeychain.saveToStorage(); // overwrite storage with the new keychain

                },
                error: function(jqXHR) {
                    scope.error = jqXHR.responseText;
                    scope.toggleLoader(false);
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
    }
});