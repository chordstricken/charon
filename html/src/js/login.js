
Vue.component('login-form', {
    template: '#login-form-template',
    data: function() {
        return {
            error: '',
            email: '',
            pass: '',
        };
    },
    methods: {
        loginAttempt: function (e) {
            e.preventDefault();

            var scope = this;

            UserKeychain.setPassword(scope.pass);

            var ajaxData = {
                email: scope.email,
                passhash: UserKeychain.PassHash,
            };

            // clear error
            scope.error = '';

            $.post({
                url: '/login',
                data: json_encode(ajaxData),
                dataType: 'json',
                success: function(result) {
                    UserKeychain.setContentKey(result.contentKeyEncrypted);
                    if (UserKeychain.ContentKey) {
                        UserKeychain.saveToStorage();
                        location.reload();
                    } else {
                        scope.error = 'Error decrypting ContentKey';
                    }
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    scope.error = jqXHR.responseText;
                }
            });

        },
    }
});

var vue_container = new Vue({
    el: '#page-container',
});