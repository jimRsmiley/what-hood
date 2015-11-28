module.exports = function(grunt) {
    grunt.initConfig({
        coffee: {
            compile: {
                files: {
                  'app/public/js/whathood/whathood-compiled.js': [
                    // always first
                    'src/coffee/whathood.coffee',
                    // classes
                    'src/coffee/Whathood/TemplateManager.coffee',
                    'src/coffee/Whathood/UrlBuilder.coffee',
                    'src/coffee/Whathood/Geo.coffee',
                    'src/coffee/Whathood/GeoSearch.coffee',
                    'src/coffee/Whathood/AddUserPolygonForm.coffee',
                    'src/coffee/Whathood/Util.coffee',
                    'src/coffee/Whathood/Page.coffee',
                    'src/coffee/Whathood/LeafletControl.coffee',
                    'src/coffee/Whathood/Search.coffee',
                    'src/coffee/Whathood/Map.coffee',
                    'src/coffee/Whathood/RegionMap.coffee',
                    'src/coffee/Whathood/UserPolygonMap.coffee',
                    'src/coffee/Whathood/DrawMap.coffee',
                    // everything else
                    'src/coffee/**/*.coffee',
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
                  'src/coffee/**/*.coffee'
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
