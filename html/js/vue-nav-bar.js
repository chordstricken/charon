Vue.component('nav-bar', {
    template: '#tmpl-nav-bar',
    props: {

    },
    data: function() {
        return {
            pagename: location.pathname,
            appname: 'Charon',
            user: {
                name: ''
            }
        }
    },
    created: function() {
        var scope = this;
        $.get('/profile', function(result) {
            scope.user = $.extend(scope.user, json_decode(AES.decrypt(result)));
        });
    },
    computed: {},
    methods: {
        logout: window.logout,
    }
});
