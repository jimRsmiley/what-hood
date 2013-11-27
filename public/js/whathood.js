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

    console.log( pts );
    
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

/*
 * given a lat,lng, returns the region
 */
/*getRegion = function(latitude,longitude) {
    
    var url = 'http://maps.googleapis.com/maps/api/geocode/json?latlng='
        +latitude
        +','
        +longitude
        +'&sensor=true';
    
    var retVal = null;
    $.ajax({
        async:false,
        url: url,
        context: document.body,
        success: function(json) {
            var regionType = "locality"; // what google calls what we're looking for
            
            for( var i in json.results ) {
                
                //console.log( json.results[i] );
                
                var formattedAddress = json.results[i].formatted_address;
                //console.log( json.results[i].types );
                
                for( var j in json.results[i].types ) {
                    var type = json.results[i].types[j];
                    //console.log( formattedAddress + " : " + type );
                    
                    if( type == regionType ) {
                        retVal = formattedAddress;
                    }
                }
            }
            
            console.log( retVal );
            var pat = /([^,]+),.;
            // only pull in up to the comma
            retVal = retVal.replace( pat, "$1" );
        },
        failure: function(result) {
            console.log("reverse geocoding failed for latitude " 
                + latitude
                + " longitude " 
                + longitude );
        }
    });
    
    console.log( "returning " + retVal );
    return retVal;
}*/

function WhathoodMap( options ) 
{
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
     * GeoJson
     */
    if( typeof WH.geoJson !== 'undefined' ) {
        console.log( 'WH.init(): adding geoJson' );
        WH.addGeoJson( WH.geoJson );
    }
    
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
                //WH.map.fitBounds( WH.geoJsonLayer.getBounds() );
                WH.geoJsonLayer.on('click', WH.mapClickEventHandler );
            },
            failure: function() {
                console.log( "WH.init(): something went wrong loading json source" );
            }
        }).done( function() {
            WH.map.spin(false);
        });
    }
    
    
    /*
     * HEATMAP
     */
    if( typeof WH.heatMapData !== 'undefined' ) {
        WH.addHeatMap( WH.options.heatMapMax, WH.heatMapData );
    }
    
    /*
     *  CENTER MARKER
     */
    if( WH.placeCenterMarker && ( typeof WH.center ) !== 'undefined' ) {
        console.log( 'WH.init(): setting center ' + WH.center );
        L.marker(new L.LatLng(WH.center[0], WH.center[1])).addTo(WH.map);
    }
    
    WH.map.on( "zoomend", function( e ) {
        console.log( 'zoom now at: ' + WH.map.getZoom() );
    });

} // end constructor

WhathoodMap.prototype.setOptions = function( options ) {
    WH.options = options;
    WH.cssId                = WH.options.cssId;
    WH.geoJsonSrc           = WH.options.geoJsonSrc;
    WH.geoJson              = WH.options.geoJson;
    WH.placeCenterMarker    = WH.options.placeCenterMarker;
    WH.heatMapData          = WH.options.heatMapData;
    WH.whathoodOnClick      = WH.options.whathoodOnClick;
    WH.zoom                 = WH.options.zoom;
    WH.cloudmadeUrl         = 'http://{s}.tile.cloudmade.com/BC9A493B41014CAABB98F0471D759707/997/256/{z}/{x}/{y}.png',
    WH.defaultRegionZoom    = 10;
    WH.neighborhoodZoom     = 13;
    WH.center               = WH.options.center; // default center
    WH.locationMarker       = null;
    WH.neighborhoodLayer    = null;
    WH.heatMapLayer         = null;
}

WhathoodMap.prototype.addGeoJson = function( geoJson ) {
    WH.geoJsonLayer = L.geoJson(geoJson, {
        coordsToLatLng: function(coords) {
            return new L.LatLng(coords[0],coords[1]);
        }
    }).addTo(WH.map);
    WH.map.fitBounds( WH.geoJsonLayer.getBounds() );
}

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

    var searchUrl = "/whathood?"+'lat='+lat+'&lng='+lng+'&format=json';
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
    WH.map.options.minZoom = 14; WH.map.options.maxZoom = 16;
    //WH.map.options.minZoom = 14; WH.map.options.maxZoom = 15;
    WH.heatMapLayer.setData( max, data );
    WH.heatMapLayer.addTo( WH.map );

    //WH.map.panTo( new L.LatLng(lat, lng), 8 );
    WH.map.fitBounds( WH.heatMapLayer );
    console.log( "WH.addHeatMap(): current zoom " + WH.map.getZoom() );
}

function WhathoodVote(options) {
    WV = this;
    WV.options = options;
    WV.neighborhoodPolygonId = options.neighborhoodPolygonId;
    console.log( 'WV.init()' );
    console.log( 'WV.init(): userLoggedIn: ' + options.userLoggedIn );
    
    WV.updateVotes();
}

WhathoodVote.prototype.cast = function( direction ) {
    $.ajax( {
        type: "POST",
        url: '/n/vote/cast',
        data: {
            neighborhoodPolygonId: WV.neighborhoodPolygonId,
            vote: direction
        },
        success: function(data) {
            console.log( data );
            WV.updateVotes();

        },
        failure: function() {
            console.log( "boo that didn't work");
        },
        error: function (xhr, ajaxOptions, thrownError) {
            console.log(xhr);
            console.log(xhr.status);
            console.log( 'response text: ' + xhr.responseText );
            console.log(thrownError);
        }
    });
}

/*
 * pass this thing up, or down, or it will default to no vote set
 * @param {type} voteDirection
 * @returns {undefined}
 */
WhathoodVote.prototype.setUserVoteButtons = function( voteDirection ) {
    console.log( 'WV.setUserVoteButtons(): voteDirection=' + voteDirection );
                
    if( voteDirection == 'up' ) {
        $(WV.options.upButtonId).addClass('active');
        $(WV.options.downButtonId).removeClass('active');
        $(WV.options.userVoteMessageId).html("You voted up");
    }
    else if( voteDirection == 'down' ) {
        $(WV.options.upButtonId).removeClass('active');
        $(WV.options.downButtonId).addClass('active');
        $(WV.options.userVoteMessageId).html("You voted down");
    }
    else {
        $(WV.options.upButtonId).removeClass('active');
        $(WV.options.downButtonId).removeClass('active');
        $(WV.options.userVoteMessageId).html("You haven't voted on this neighborhood");
    }
}

WhathoodVote.prototype.updateVotes = function() {
    console.log( "WV.updateVotes() : updating votes");

    var url ='/n/vote/by-neighborhood-polygon-id?neighborhoodPolygonId='+WV.neighborhoodPolygonId;
    console.log( url );
    $.ajax( {
        url: url,
        success: function( data ) {
            var voteResult = data.voteResult;
            console.log( voteResult );
            var numVotesUp = voteResult.allVotes.upCount;
            var numVotesDown = voteResult.allVotes.downCount;

            /*
             * display the vote count
             */
            $('.vote_results').html( 
                numVotesUp + " Vote".pluralize(numVotesUp) + " Up " 
                + " - "
                + numVotesDown + " Vote".pluralize(numVotesDown) + " Down" 
            );
                
                
            /*
             * set correct active states on the vote buttons
             */
            if( voteResult.thisUsersVote ) {
                thisUsersVote = voteResult.thisUsersVote;
                console.log( "WV.updateVotes(): user logged in, their vote: " + thisUsersVote.direction );
                WV.setUserVoteButtons(thisUsersVote.direction);
            }
            else {
                console.log( "WV.updateVotes(): user not logged in" );
            }
        },
        error: function (xhr, ajaxOptions, thrownError) {
            console.log( 'WV.updateVotes() : ajax failed, messages follows' );
            console.log(xhr);
            console.log(xhr.status);
            console.log( 'update votes text: ' + xhr.responseText );
            console.log(thrownError);
        }
    });
}