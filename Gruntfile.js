module.exports = function(grunt) {
    grunt.initConfig({
        coffee: {
            compile: {
                files: {
                  'app/public/js/whathood/whathood-compiled.js': [
                    // always first
                    'coffeescript/whathood.coffee',
                    'coffeescript/Whathood/Page.coffee',
                    'coffeescript/Whathood/Search.coffee',
                    'coffeescript/Whathood/Map.coffee',
                    'coffeescript/Whathood/RegionMap.coffee',
                    'coffeescript/Whathood/UserPolygonMap.coffee',
                    'coffeescript/Whathood/NeighborhoodHeatMap.coffee',
                    'coffeescript/Whathood/DrawMap.coffee',
                    'coffeescript/address-search.coffee',
                  ]
                }
            }
        },
        watch: {
            coffee: {
                files: [
                  'coffeescript/*.coffee',
                  'coffeescript/Whathood/*.coffee',
                ],
                tasks: 'coffee:compile'
            }
        }
    });

    grunt.loadNpmTasks('grunt-contrib-watch');
    grunt.loadNpmTasks('grunt-contrib-coffee');

    grunt.registerTask('default',['coffee:compile', 'watch']);
};
