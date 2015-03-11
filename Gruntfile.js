module.exports = function(grunt) {
    grunt.initConfig({
        coffee: {
            compile: {
                files: {
                  'app/public/js/whathood/whathood-compiled.js': [
                    // always first
                    'coffeescript/whathood.coffee',
                    // classes
                    'coffeescript/Whathood/GeoSearch.coffee',
                    'coffeescript/Whathood/AddUserPolygonForm.coffee',
                    'coffeescript/Whathood/Util.coffee',
                    'coffeescript/Whathood/Page.coffee',
                    'coffeescript/Whathood/LeafletControl.coffee',
                    'coffeescript/Whathood/Search.coffee',
                    'coffeescript/Whathood/Map.coffee',
                    'coffeescript/Whathood/RegionMap.coffee',
                    'coffeescript/Whathood/UserPolygonMap.coffee',
                    'coffeescript/Whathood/NeighborhoodHeatMap.coffee',
                    'coffeescript/Whathood/DrawMap.coffee',
                    // the pages
                    'coffeescript/*.coffee',
                  ]
                }
            }
        },
        clean: [
            'app/public/js/whathood/whathood-compiled.js'
        ],
        watch: {
            coffee: {
                files: [
                  'coffeescript/*.coffee',
                  'coffeescript/Whathood/*.coffee',
                ],
                tasks: ['clean','coffee:compile']
            }
        }
    });

    grunt.loadNpmTasks('grunt-contrib-watch');
    grunt.loadNpmTasks('grunt-contrib-coffee');
    grunt.loadNpmTasks('grunt-contrib-clean');

    grunt.registerTask('default',['coffee:compile', 'watch']);
};
