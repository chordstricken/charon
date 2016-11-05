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
        uglify: {
            options: {
                mangle: false
            },
            dist: {
                files: {
                    'html/js/app.min.js': [
                        'html/js/mcrypt.js',
                        'html/js/rijndael.js',
                        'html/js/md5.js',
                        'html/js/sha256.js',
                        'html/js/jquery.min.js',
                        'html/js/bootstrap.min.js',
                        'html/js/angular.min.js',
                        'html/js/angular-sortable-view.min.js',
                        'html/js/functions.js',
                        'html/js/home.js'
                    ]
                }
            }
        },
        concat: {
            options: {
                separator: "\n",
            },
            dist: {
                src: [
                    'html/css/bootstrap-flatly.min.css',
                    'html/css/font-awesome.min.css',
                    'html/css/home.css',
                ],
                dest: 'html/css/app.min.css',
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

    grunt.registerTask('default', ['jshint', 'concat', 'uglify']);
    grunt.registerTask('watch', ['watch']);

};
