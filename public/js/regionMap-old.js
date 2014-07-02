
function RegionMap( options ) 
{
    
    function style(feature) {
        return {
            weight: 2,
            opacity: 1,
            color: 'white',
            dashArray: '3',
            fillOpacity: 1,
            fillColor: getColor(feature.properties.percentChange)
        };
    }

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

        update(layer.feature.properties);
    }

    var geojson;

    function resetHighlight(e) {
        geoJsonLayer.resetStyle(e.target);
        update();
    }

    function onEachFeature(feature, layer) {
        layer.on({
            mouseover: highlightFeature,
            mouseout: resetHighlight,
        });
    }

    geoJsonLayer = L.geoJson(geoJson, {
        style: style,
        onEachFeature: onEachFeature
    }).addTo(map);

    map.fitBounds( geoJsonLayer, { padding: [0,0] } );
    
    WH = this; WH.setOptions( options );
    
    if( typeof WH.zoom === 'undefined' )
        WH.zoom = WH.defaultRegionZoom;
    
    var cloudmadeLayer = new L.TileLayer(
            WH.cloudmadeUrl, 
            {
                attribution: 'Map data &copy; 2011 OpenStreetMap contributors, Imagery &copy; 2011 CloudMade',
                maxZoom: 18
            }
        );
    
    console.log( 'WH.init(): center: ' + WH.center + ' zoom: ' + WH.zoom );
    WH.map = new L.Map(
        WH.cssId, 
        {
            layers: [cloudmadeLayer],
            center: WH.center,
            zoom:   WH.zoom 
        }
    );
    
    /*
     * WHATHOOD MAP CLICK
     */
    if( typeof WH.whathoodOnClick !== 'undefined' ) {
        console.log( "WH.init(): adding whathoodOnClick");
        WH.addWhathoodClick();
    }
    
    /*
     * load GEOJsonSrc from url
     */
    if( typeof WH.geoJsonSrc !== 'undefined' ) {
        WH.map.spin(true);
        console.log( "WH.init() geoJson source: http://whathood.in" + WH.geoJsonSrc );
        $.ajax({
            url: this.options.geoJsonSrc,
            success: function(json) {
                WH.addGeoJson( json );
                WH.map.fitBounds( WH.geoJsonLayer.getBounds() );
                WH.geoJsonLayer.on('click', WH.mapClickEventHandler );
            },
            failure: function() {
                console.log( "WH.init(): something went wrong loading json source" );
            }
        }).done( function() {
            WH.map.spin(false);
        });
    }
    
} // end constructor


/*
* what do we show in the marker popup
*/
WhathoodMap.prototype.getPopup = function(json,regionName) {

    var neighborhoods = json.whathood_result.response.consensus.neighborhoods;
    var requestLat = json.whathood_result.request.lat;
    var requestLng = json.whathood_result.request.lng;
    
    html = '';
    for( n in neighborhoods ) {
        var name = neighborhoods[n].name;
        var votes = neighborhoods[n].votes
        html += votes + ' ' + "vote".pluralize(votes) + ' for ' + name + '<br/>';
    }
    
    url = WH.getNeighborhoodBrowseUrl(requestLat,requestLng);
    
    if( typeof regionName !== 'undefined' ) {
        console.log('WH.getPopup(): regionName: ' + regionName );
        url += '&region_name='+regionName;
    }
    html += '<a href="'+url+'">Browse these neighborhoods</a>';
    return html;
}   

WhathoodMap.prototype.getNeighborhoodBrowseUrl = function(lat,lng) {
    return '/n/page/1/center/'+lat+','+lng;
}

/*
 * add the ability to click on the map, and have a whathood popup telling what
 * neighborhoods it matches
 */
WhathoodMap.prototype.addWhathoodClick = function() {
    WH.locationMarker = null;
    WH.map.on('click', WH.mapClickEventHandler );
}

/*
 * what happens when someone clicks the map?
 */
WhathoodMap.prototype.mapClickEventHandler = function(e) {

    var lat = e.latlng.lat; var lng = e.latlng.lng;

    if( WH.locationMarker !== null ) {
        WH.map.removeLayer(WH.locationMarker);
    }

    WH.locationMarker = L.marker([lat, lng])
            .addTo(WH.map);
    WH.locationMarker.bindPopup('<div id="map_popup" style="overflow:auto; width: 40px; height: 40px"><img src="/images/spiffygif_30x30.gif" alt="loading..."/></div>')
            .openPopup();

    var searchUrl = "/whathood-search?"+'lat='+lat+'&lng='+lng+'&format=json';
    console.log( 'WH.mapClickEventHandler(): ' + 'http://whathood.in' + searchUrl );
    $.ajax({
        url: searchUrl,
        context: document.body,
        success: function(data) {
            WH.locationMarker.bindPopup(
                    WH.getPopup(data,WH.regionName)
            ).openPopup();
        }
    });
}


/*
 * Leaflet.Draw stuff
 */
WhathoodMap.prototype.addLeafletDraw = function() {
    WH.drawnItems = new L.FeatureGroup();
    WH.map.addLayer(WH.drawnItems);
    
    WH.map.addControl( WH.getDrawControl(WH.cloudMade) );

    WH.map.on('draw:created', function (e) {
        var type = e.layerType,
            layer = e.layer;

        if (type === 'marker') {
            layer.bindPopup('A popup!');
        }

        WH.drawnItems.addLayer(layer);

        WH.neighborhoodLayer = layer;
    });

    WH.map.on('draw:edited', function (e) {
        var layers = e.layers;
        var countOfEditedLayers = 0;
        layers.eachLayer(function(layer) {
            countOfEditedLayers++;
        });
        console.log("Edited " + countOfEditedLayers + " layers");
    });
}

WhathoodMap.prototype.getDrawControl = function(neighborhoodLayer) {

    var editableLayers = null;
    if( WH.neighborhoodLayer !== null ) {
        editableLayers = new L.FeatureGroup([WH.neighborhoodLayer]);
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
}

WhathoodMap.prototype.getDrawnGeoJson = function() {
    return WH.neighborhoodLayer.toGeoJSON();
}

WhathoodMap.prototype.addHeatMap = function( max, data ) {
    console.log( 'WH#addHeatMap: max:'+max );
    
    WH.heatMapLayer = L.TileLayer.heatMap({
        radius: 14,
        opacity: 0.70,
        gradient: {
            0.45: "rgb(0,0,255)",
            0.55: "rgb(0,255,255)",
            0.65: "rgb(0,255,0)",
            0.90: "yellow",
            1.0: "rgb(255,0,0)"
        }
    });
    WH.map.options.minZoom = 12; WH.map.options.maxZoom = 18;
    //WH.map.options.minZoom = 14; WH.map.options.maxZoom = 15;
    WH.heatMapLayer.setData( max, data );
    WH.heatMapLayer.addTo( WH.map );

    //WH.map.panTo( new L.LatLng(lat, lng), 8 );
    WH.map.fitBounds( WH.heatMapLayer );
    console.log( "WH.addHeatMap(): current zoom " + WH.map.getZoom() );
}