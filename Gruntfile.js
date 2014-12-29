module.exports = function(grunt) {
    grunt.initConfig({
        coffee: {
            compile: {
                files: {
                    'app/public/js/whathood/whathood-compiled.js': [
                      'coffeescript/whathood.coffee',
                      'coffeescript/address-search.coffee'
                    ]
                }
            }
        },
        watch: {
            coffee: {
                files: ['coffeescript/*.coffee'],
                tasks: 'coffee:compile'
            }
        }
    });

    grunt.loadNpmTasks('grunt-contrib-watch');
    grunt.loadNpmTasks('grunt-contrib-coffee');

    grunt.registerTask('default',['coffee:compile', 'watch']);
};
