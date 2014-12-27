module.exports = function(grunt) {
    grunt.initConfig({
        coffee: {
            compile: {
                files: {
                    'app/public/js/whathood-compiled.js': [ 'coffee/src/*.coffee' ]
                }
            }
        },
        watch: {
            coffee: {
                files: ['coffee/src/*.coffee'],
                tasks: 'coffee:compile'
            }
        }
    });

    grunt.loadNpmTasks('grunt-contrib-watch');
    grunt.loadNpmTasks('grunt-contrib-coffee');

    grunt.registerTask('default',['coffee:compile', 'watch']);
};
