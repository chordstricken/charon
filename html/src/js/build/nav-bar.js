Vue.component('nav-bar', {
    template: '#tmpl-nav-bar',
    props: {
        pageTitle: String
    },
    data: function() {
        return {
            pagename: location.pathname,
            appname: 'Charon',
            user: {
                name: '',
                permLevel: 0,
            }
        };
    },
    created: function() {
        var scope = this;
        $.get('/profile', function(result) {
            scope.user = $.extend(scope.user, json_decode(AES.decrypt(result)));
        });
    },
    computed: {},
    methods: {
        hasPermission: function(perms) {
            perms = perms.indexOf ? perms : [perms];
            return (this.user.permLevel == 1 || perms.indexOf(this.user.permLevel) !== -1);
        },
        logout: window.logout,
    }
});
