module.exports = function(grunt) {

    require('load-grunt-tasks')(grunt); // npm install --save-dev load-grunt-tasks

    grunt.initConfig({
        jshint: {
            files: ['Gruntfile.js'],
            options: {
                globals: {
                    jQuery: true
                }
            }
        },
        concat: {
            options: {
                separator: "\n",
            },
            css: {
                src: [
                    'bower_components/bootstrap/dist/css/bootstrap.css',
                    'bower_components/font-awesome/css/font-awesome.css',
                    'html/css/bootstrap-flatly.min.css',
                    'html/css/main.css',
                ],
                dest: 'html/css/build.css',
            },
            js: {
                src: [
                    // jquery & addons
                    'bower_components/jquery/dist/jquery.js',

                    // vuejs & addons
                    'bower_components/vue/dist/vue.js',
                    'bower_components/Sortable/Sortable.js',
                    'bower_components/vue.draggable/dist/vuedraggable.min.js',

                    // bootstrap & addons
                    'bower_components/bootstrap/dist/js/bootstrap.js',

                    // encryption & utils
                    'html/js/cryptojs/rollups/aes.js',
                    'html/js/cryptojs/components/mode-ctr-min.js',
                    'html/js/cryptojs/rollups/md5.js',
                    'html/js/jsencrypt.js',
                    'html/js/functions.js',
                    'html/js/vue-navbar.js',
                ],
                dest: 'html/js/build.js',
            },
        },
        uglify: {
            options: {
                mangle: false
            },
            dist: {
                files: {
                    'html/js/build.min.js': 'html/js/build.js',
                    'html/js/locker.min.js': 'html/js/locker.js',
                    'html/js/login.min.js': 'html/js/login.js',
                }
            }
        },
        copy: {
            fonts: {
                expand: true,
                src: ['bower_components/font-awesome/fonts/*'],
                dest: 'html/fonts/',
            },
        },
        watch: {
            files: ['<%= jshint.files %>', '<%= sass.dist.files %>'],
            tasks: ['jshint', 'sass', 'uglify']
        }
    });

    grunt.loadNpmTasks('grunt-contrib-jshint');
    grunt.loadNpmTasks('grunt-contrib-watch');
    grunt.loadNpmTasks('grunt-contrib-uglify');
    grunt.loadNpmTasks('grunt-contrib-concat');

    grunt.registerTask('default', ['jshint', 'concat', 'uglify', 'copy']);
    grunt.registerTask('watch', 'watch');

};
