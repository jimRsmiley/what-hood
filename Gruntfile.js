module.exports = function(grunt) {
    grunt.initConfig({
        BUILD_DIR: "./build/js",
        DIST_DIR: "app/public/js",
        babel: {
            options: {
                plugins: ['transform-react-jsx'], // npm install babel-plugin-transform-react-jsx
                presets: ['es2015', 'react'] // npm install babel-preset-es2015 babel-preset-react
            },
            jsx: {
                      files: [{
                                expand: true,
                                cwd: 'libs/react/',
                                src: ['*.jsx'],
                                dest: 'build/react/',
                                ext: '.js'
                              }]
            }
        },
        coffee: {
            compile: {
                files: {
                  '<%= BUILD_DIR %>/whathood-compiled.js': [
                    // always first
                    'src/coffee/whathood.coffee',
                    // classes
                    'src/coffee/Whathood/UrlBuilder.coffee',
                    'src/coffee/Whathood/Geo.coffee',
                    'src/coffee/Whathood/GeoSearch.coffee',
                    'src/coffee/Whathood/AddUserPolygonForm.coffee',
                    'src/coffee/Whathood/Util.coffee',
                    'src/coffee/Whathood/Page.coffee',
                    'src/coffee/Whathood/LeafletControl.coffee',
                    'src/coffee/Whathood/Search.coffee',
                    'src/coffee/Whathood/Map.coffee',
                    'src/coffee/Whathood/UserPolygonMap.coffee',
                    'src/coffee/Whathood/DrawMap.coffee',
                    // everything else
                    'src/coffee/**/*.coffee',
                  ]
                }
            }
        },
        uglify: {
          vendors : {
            files : {
              '<%= DIST_DIR %>/vendors.js' : [
                'libs/javascript/spin.min.js',
                'libs/javascript/leaflet.spin.js',
                'libs/javascript/leaflet.draw.js',
                'libs/javascript/Leaflet.markercluster/dist/leaflet.markercluster.js',
                'libs/javascript/leaflet-plugins/L.GeoSearch/src/js/l.control.geosearch.js',
                'libs/javascript/leaflet-plugins/L.GeoSearch/src/js/l.geosearch.provider.openstreetmap.js',
                'libs/javascript/leaflet-plugins/L.GeoSearch/src/js/l.geosearch.provider.google.js',
                'libs/javascript/heatmap.min.js',
                'libs/javascript/leaflet-heatmap.js',
                'libs/javascript/DataTables-1.10.8/media/js/jquery.dataTables.js',
              ]
            }
          },
          whathood : {
            options : {
              sourceMap : true,
              sourceMapName : 'app/public/js/sourcemap/whathood.map'
            },
            files : {
              '<%= DIST_DIR %>/bundle.js' : [
                'libs/javascript/whathood.js',
                '<%= BUILD_DIR %>/whathood-compiled.js',
                '<%= BUILD_DIR %>/../react/WhathoodReact.js',
                'libs/javascript/whathood.js'
              ]
            }
          }
        },
        less: {
            development: {
                files: {
                  "app/public/whathood.css": [
                      'libs/javascript/leaflet-draw/css/leaflet.draw.css',
                      'libs/javascript/DataTables-1.10.8/media/css/jquery.dataTables.css',
                      'libs/javascript/Leaflet.markercluster/dist/MarkerCluster.css',
                      'libs/javascript/Leaflet.markercluster/dist/MarkerCluster.Default.css',
                      "src/less/whathood.less"
                  ]
                }
            }
        },
        clean: {
            coffee: ['app/public/js/whathood/whathood-compiled.js'],
            less:   ['app/public/css/whathood-less.css']
        },
        watch: {
            javascript: {
                files: [
                  'src/coffee/**/*.coffee'
                ],
                tasks: ['javascript']
            },
            css: {
                files: [
                  'src/less/**/*.less' ],
                tasks: ['css']
            }
        },
        cssmin: {
          options: {
            mergeIntoShorthands: false,
            roundingPrecision: -1
          },
          target: {
            files: {
              'app/public/css/whathood.css': [
                'libs/javascript/**/*.css',
              ]
            }
          }
        }
    });

    grunt.loadNpmTasks('grunt-contrib-watch');
    grunt.loadNpmTasks('grunt-contrib-coffee');
    grunt.loadNpmTasks('grunt-contrib-clean');
    grunt.loadNpmTasks('grunt-contrib-less');
    grunt.loadNpmTasks('grunt-angular-builder');
    grunt.loadNpmTasks('grunt-contrib-uglify');
    grunt.loadNpmTasks('grunt-contrib-cssmin');
    grunt.loadNpmTasks('grunt-babel');
    grunt.loadNpmTasks('grunt-babel');


    // setup foreman
    grunt.loadNpmTasks("grunt-foreman");
    grunt.registerTask("serve", "foreman");
    grunt.registerTask('css', ['less']);

    grunt.registerTask('javascript', ['uglify:vendors', 'babel', 'coffee:compile', 'uglify:whathood']);
};
