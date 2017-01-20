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
                    'html/css/bootstrap-flatly.min.css',
                    'html/css/charon.css',
                ],
                dest: 'html/css/charon.min.css',
            },
            js: {
                src: [
                    'html/js/cryptojs/rollups/aes.js',
                    'html/js/cryptojs/components/mode-ctr-min.js',
                    'html/js/cryptojs/rollups/md5.js',
                    'html/js/jsencrypt.js',
                    'html/js/functions.js',
                ],
                dest: 'html/js/charon.js',
            },
        },
        uglify: {
            options: {
                mangle: false
            },
            dist: {
                files: {
                    'html/js/charon.min.js': 'html/js/charon.js',
                    'html/js/locker.min.js': 'html/js/locker.js',
                    'html/js/login.min.js': 'html/js/login.js',
                }
            }
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

    grunt.registerTask('default', ['jshint', 'concat', 'uglify']);
    grunt.registerTask('watch', 'watch');

};
