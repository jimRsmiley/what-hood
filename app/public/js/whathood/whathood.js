var mapbox_map_id = 'jimrsmiley.k5eep890';

String.prototype.pluralize = function(count, plural)
{
  if (plural == null)
    plural = this + 's';

  return (count == 1 ? this : plural)
}

L.TileLayer.HeatMap.prototype.getBounds = function() {
     var self = this;
     return self._bounds;
}

L.GeoJSON.prototype.getCenter = function(){
    var pts = this._latlngs;

    var twicearea = 0;
    var p1, p2, f;
    var x = 0, y = 0;
    var nPts = pts.length;

    for(var i=0, j=nPts-1;i<nPts;j=i++) {
        p1=pts[i];
        p2=pts[j];
        twicearea+=p1.lat*p2.lng;
        twicearea-=p1.lng*p2.lat;

        f=p1.lat*p2.lng-p2.lat*p1.lng;

        x+=(p1.lat+p2.lat)*f;
        y+=(p1.lng+p2.lng)*f;
    }
    f=twicearea*3;
    return {lat: x/f,lng: y/f};
}


function getRandomColor() {
    var color = "#"+Math.floor(Math.random()*16777215).toString(16);
    return color;
}


function getURLParameter(name) {
    return decodeURI(
        (RegExp(name + '=' + '(.+?)(&|$)').exec(location.search)||[,null])[1]
    );
}

/**

functions:

    addStreetLayer() - adds the street layer

    addGeoJson( url, callback ) - add geojson to the map from the url

**/
var NewWhathoodMap = L.Map.extend( {

    _layerGroup: null,
    _geojsonTileLayer : null,

    layerGroup: function() {
        if( this._layerGroup === null ) {
            this._layerGroup = new L.LayerGroup();
        }
        return this._layerGroup;
    },

    debugLayers : function() {

        this.layerGroup().eachLayer( function (layer) {
            if( layer.options.id == 'contentious' ) {
                layer.bringToFront();
            }
        });
    },

    addStreetLayer : function() {
        streetLayer = L.tileLayer('https://{s}.tiles.mapbox.com/v3/{id}/{z}/{x}/{y}.png', {
            maxZoom: 18,
            attribution: 'Map data &copy; <a href="http://openstreetmap.org">OpenStreetMap</a> contributors | ' +
                '<a href="http://creativecommons.org/licenses/by-sa/2.0/">CC-BY-SA</a> | ' +
                'Imagery © <a href="http://mapbox.com">Mapbox</a> | ' +
                'Neighborhood borders provided by <a href="http://www.azavea.com/blogs/newsletter/v8i2/philly-neighborhoods-map/">Azavea</a>',
            id: mapbox_map_id
        }).addTo(this);
        this.layerGroup().addLayer( streetLayer );
        this.centerOnRegion();
    },

    addGeoJson: function ( url, callback ) {

        var self = this;

        $.ajax({
            url: url,
            success: function(geojson) {

                // control that shows state info on hover
                var info = L.control();

                info.onAdd = function (map) {
                    this._div = L.DomUtil.create('div', 'info');
                    this.update();
                    return this._div;
                };

                info.update = function (props) {

                    this._div.innerHTML = '<h4>What Hood Neighborhoods</h4>' +  (props ?
                        '<b>' + props.name + '</b><br />'
                                +'<b>Number of users who contributed to these borders:</b>'+props.num_user_polygons+'<br/>'
                                +'<br/>'
                                +'<a href="/Philadelphia/'+props.name+'">Go to ' + name + " identity heatmap</a>"
                        : 'Click a neighborhood');
                };

                info.addTo(self);

                function style(feature) {
                    return {
                        weight: 3,
                        opacity: 1,
                        color: 'white',
                        dashArray: '3',
                        fillOpacity: 0.7,
                        fillColor: '#FEB24C'
                    };
                };

                function highlightFeature(e) {
                    var layer = e.target;

                    layer.setStyle({
                        weight: 5,
                        color: '#666',
                        dashArray: '',
                        fillOpacity: 0.7
                    });

                    if (!L.Browser.ie && !L.Browser.opera) {
                        layer.bringToFront();
                    }
                    updateInfo(e);
                }

                function updateInfo(e) {
                    var layer = e.target;
                    info.update(layer.feature.properties);
                }

                function resetHighlight(e) {
                    self.geojsonLayer.resetStyle(e.target);
                }
                function onEachFeature(feature,layer) {
                    layer.on({
                        mouseover: highlightFeature,
                        mouseout: resetHighlight
                    });
                };

                self.geojsonLayer = new L.geoJson(geojson, {
                    style: style,
                    id: 'geojson',
                    onEachFeature: onEachFeature
                }).addTo(self);
                self.layerGroup().addLayer( self.geojsonLayer );
                self.fitBounds( self.geojsonLayer );

                if( ( typeof callback ) != 'undefined' ) {
                    callback();
                }
            },
            failure: function() {
                console.log( "WH.init(): something went wrong loading json source" );
            }
        }).done( function() {
            self.spin(false);
        });
    },

    centerOnRegion: function() {
        this.setView([39.9505, -75.148], 12);
    },

    /*
    * add the ability to click on the map, and have a whathood popup telling what
    * neighborhoods it matches
    */
    whathoodClick : function( bool ) {

        if( bool !== true )
            return;

        self = this;
        self.locationMarker = null;

        var getNeighborhoodBrowseUrl = function(lat,lng) {
            return '/n/page/1/center/'+lat+','+lng;
        };

        var getPopup = function(json,regionName) {

            var neighborhoods = json.whathood_result.response.consensus.neighborhoods;
            var requestLat = json.whathood_result.request.lat;
            var requestLng = json.whathood_result.request.lng;

            html = '';
            for( n in neighborhoods ) {
                var name = neighborhoods[n].name;
                var votes = neighborhoods[n].votes
                html += votes + ' ' + "vote".pluralize(votes) + ' for ' + name + '<br/>';
            }

            url = getNeighborhoodBrowseUrl(requestLat,requestLng);

            if( typeof regionName !== 'undefined' ) {
                url += '&region_name='+regionName;
            }
            html += '<a href="'+url+'">Browse these neighborhoods</a>';
            return html;
        };

        var mapClickEventHandler = function(e) {
            var lat = e.latlng.lat; var lng = e.latlng.lng;

            if( self.locationMarker !== null ) {
                self.removeLayer(self.locationMarker);
            }
            self.locationMarker = L.marker([lat, lng])
                    .addTo(self);
            self.locationMarker.bindPopup('<div id="map_popup" style="overflow:auto; width: 40px; height: 40px"><img src="/images/spiffygif_30x30.gif" alt="loading..."/></div>')
                    .openPopup();

            var searchUrl = "/whathood-search?"+'lat='+lat+'&lng='+lng+'&format=json';
            $.ajax({
                url: searchUrl,
                context: document.body,
                success: function(data) {

                    self.locationMarker.bindPopup(
                        getPopup(data,self.regionName)
                    ).openPopup();
                }
            });
        } // end mapClickEventHandler

        self.on('click', mapClickEventHandler );
    }
} );

var WhathoodDrawMap = NewWhathoodMap.extend( {

    drawnItems : null,
    neighborhoodLayer : null,

    addLeafletDraw : function() {
        this.drawnItems = new L.FeatureGroup();
        this.addLayer(this.drawnItems);

        this.addControl( this.getDrawControl() );

        this.on('draw:created', function (e) {
            var type = e.layerType,
                layer = e.layer;

            if (type === 'marker') {
                layer.bindPopup('A popup!');
            }

            this.drawnItems.addLayer(layer);

            this.neighborhoodLayer = layer;
        });

        this.on('draw:edited', function (e) {
            var layers = e.layers;
            var countOfEditedLayers = 0;
            layers.eachLayer(function(layer) {
                countOfEditedLayers++;
            });
        });

    },
    getDrawControl : function() {

        var editableLayers = null;
        if( this.neighborhoodLayer !== null ) {
            editableLayers = new L.FeatureGroup([this.neighborhoodLayer]);
        }
        else {
            editableLayers = new L.FeatureGroup();
        }

        var options = {
            draw: {
                position: 'topleft',
                polygon: {
                    title: 'Draw a neighborhood!',
                    allowIntersection: false,
                    drawError: {
                        color: '#b00b00',
                        timeout: 1000
                    },
                    shapeOptions: {
                        color: '#54564b'
                    }
                },
                circle: false,
                polyline: false,
                rectangle: false,
                marker: false
            },
            edit: {
                featureGroup: editableLayers, //REQUIRED!!
            }
        };
        return new L.Control.Draw(options);
    },
    getDrawnGeoJson : function() {
        return this.neighborhoodLayer.toGeoJSON();
    }
}); // end constructor


var RegionMap = NewWhathoodMap.extend( {

    _markerCluster : null,

    addContentiousPoints : function(createEventId, callback ) {
        self = this;

        var url = '/whathood/contentious-point/by-create-event-id?format=heatmapJsData&create_event_id='+createEventId;


        $.ajax({
            url: url,
            success: function(pointData) {

                self._markerCluster = new L.MarkerClusterGroup();

                count = 0;
                pointData.forEach( function( point, index, array ) {

                    if( ( index % 10 ) == 0 ) {
                        //console.log( "i: " + index + " lat " + point.lat + " lon " + point.lon );
                        self._markerCluster.addLayer( new L.Marker([point.lat, point.lon] ) );
                        count++;
                    }
                });

                self.addLayer(self._markerCluster );

                if( ( typeof callback ) != 'undefined' ) {
                    callback();
                }
            },
            error: function() {
                alert('unable to retreive contentious points');
            }
        });
    }

} );

/**
*
*   Heat Map
*
**/
var NeighborhoodHeatMap = NewWhathoodMap.extend( {

    heatMapLayer: null,
    data: null,
    maxValue: 100,

    addData: function( data ) {
        this.data = data;
        this.drawHeatmap(7);
        this.fitBounds( this.heatMapLayer );
        console.log( 'done loading heat map' );
    },

    drawHeatmap: function(radius) {
        this.heatMapLayer = L.TileLayer.heatMap({
            radius: {value: 43, absolute:true},
            opacity: 0.70,
            gradient: {
                0.45: "rgb(0,0,255)",
                0.55: "rgb(0,255,255)",
                0.65: "rgb(0,255,0)",
                0.90: "yellow",
                1.0: "rgb(255,0,0)"
            }
        });
        this.heatMapLayer.setData( this.maxValue, this.data );
        this.heatMapLayer.addTo( this );
    }
});
