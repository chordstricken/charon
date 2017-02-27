
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

            var scope   = this,
                ajaxData = {
                    email: scope.email,
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