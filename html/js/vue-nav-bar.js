Vue.component('nav-bar', {
    template: '#tmpl-nav-bar',
    props: {

    },
    data: function() {
        return {
            pagename: location.pathname
        }
    },
    computed: {},
    methods: {
        logout: window.logout,
    }
});
