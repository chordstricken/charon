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
                    'html/css/home.css',
                ],
                dest: 'html/css/charon.css',
            },
            js: {
                src: [
                    'html/js/mcrypt.js',
                    'html/js/rijndael.js',
                    'html/js/md5.js',
                    'html/js/sha256.js',
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
                    'html/js/home.min.js': 'html/js/home.js',
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
    grunt.registerTask('watch', ['watch']);

};
