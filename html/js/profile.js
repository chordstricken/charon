
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

        profile: {
            changePass1: '',
            changePass2: '',
        },

        timeouts: {},
    },
    created: function() {
        var scope = this;
        $.get({
            url: '/profile',
            success: function(result) {
                var decData = AES.decrypt(result);
                var decObj = json_decode(decData);

                scope.profile = $.extend(scope.profile, decObj);
                scope.toggleLoader(false);
            }
        });
    },

    computed: {
        passwordVerify: function() {
            return this.profile.changePass1.length === 0 || this.profile.changePass1.length > 12;
        },
        passwordsMatch: function() {
            return this.profile.changePass1.length === 0 || this.profile.changePass1 === this.profile.changePass2;
        }
    },

    methods: {
        saveObject: function() {
            var scope = this;

            // passwords must match
            if (!scope.passwordsMatch) {
                scope.error = 'Passwords do not match.';
                return;
            }

            scope.toggleLoader(true);

            $.post({
                url: '/profile',
                data: json_encode(AES.encrypt(scope.profile)),
                success: function(result) {
                    result = AES.decrypt(result);
                    result = json_decode(result);

                    scope.profile = $.extend(scope.profile, result);
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