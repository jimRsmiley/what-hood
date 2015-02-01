window = exports ? this
Whathood = window.Whathood

Whathood.RegionMap = Whathood.Map.extend
  _markerCluster : null,
  _neighborhood_color: '5487b8'
  addContentiousPoints : (createEventId, callback ) =>
    self = this
    url = '/whathood/contentious-point/by-create-event-id?format=heatmapJsData&create_event_id='+createEventId
    $.ajax
      url: url,
      success: (pointData) ->
        self._markerCluster = new L.MarkerClusterGroup()
        count = 0
        pointData.forEach ( point, index, array ) ->
          if( ( index % 10 ) == 0 )
            self._markerCluster.addLayer( new L.Marker([point.lat, point.lon] ) )
            count++

            self.addLayer(self._markerCluster )

            if( ( typeof callback ) != 'undefined' )
              callback()
            error: () ->
              alert('unable to retreive contentious points')

  addGeoJson: ( url, callback ) ->
    self = this
    $.ajax
        url: url,
        success: (geojson) =>
            # control that shows state info on hover
            info = L.control()
            info.onAdd = (map) ->
                this._div = L.DomUtil.create('div', 'info')
                this.update()
                return this._div
            info.update = (props) ->
              if props
                this._div.innerHTML = '<h4>What Hood Neighborhoods</h4>' +
                  '<b>' + props.name + '</b><br />'
                  +'<b>Number of users who contributed to these borders:</b>'+props.num_user_polygons+'<br/>'
                  +'<br/>'
                  +'<a href="/Philadelphia/'+props.name+'">Go to ' + name + " identity heatmap</a>"
              else
                this._div.innerHTML = '<h4>What Hood Neighborhoods</h4>' +
                  'Click a neighborhood'
            info.addTo(self)

            style = (feature) =>
              weight: 3,
              opacity: 1,
              color: 'white',
              dashArray: '3',
              fillOpacity: 0.7,
              fillColor: @_neighborhood_color

            highlightFeature = (e) ->
              layer = e.target
              layer.setStyle
                  weight: 5,
                  color: '#666',
                  dashArray: '',
                  fillOpacity: 0.7
              if (!L.Browser.ie && !L.Browser.opera)
                layer.bringToFront()
              updateInfo e

            updateInfo = (e) ->
                layer = e.target
                info.update(layer.feature.properties)

            resetHighlight = (e) ->
                self.geojsonLayer.resetStyle(e.target)

            onEachFeature = (feature,layer) ->
                layer.on({
                    mouseover: highlightFeature,
                    click: updateInfo,
                    mouseout: resetHighlight
                })

            self.geojsonLayer = new L.geoJson(geojson, {
                style: style,
                id: 'geojson',
                onEachFeature: onEachFeature
            }).addTo(self)
            self.layerGroup().addLayer( self.geojsonLayer )
            self.fitBounds( self.geojsonLayer )

            if( ( typeof callback ) != 'undefined' )
                callback()
        failure: () ->
            console.log( "WH.init(): something went wrong loading json source" )
    .done () ->
      self.spin(false)


    #/*
    #* add the ability to click on the map, and have a whathood popup telling what
    #* neighborhoods it matches
    #*/
    whathoodClick : ( bool ) ->

        if( bool != true )
            return

        self = this
        self.locationMarker = null

        getNeighborhoodBrowseUrl = (lat,lng) ->
            return '/n/page/1/center/'+lat+','+lng

        getPopup = (json,regionName) ->

            neighborhoods = json.whathood_result.response.consensus.neighborhoods
            requestLat = json.whathood_result.request.lat
            requestLng = json.whathood_result.request.lng

            html = ''
            for n in neighborhoods
              name = neighborhoods[n].name
              votes = neighborhoods[n].votes
              html += votes + ' ' + "vote".pluralize(votes) + ' for ' + name + '<br/>'

            url = getNeighborhoodBrowseUrl requestLat, requestLng

            if( typeof regionName != 'undefined' )
                url += '&region_name='+regionName
            html += '<a href="'+url+'">Browse these neighborhoods</a>'
            return html

        mapClickEventHandler = (e) ->
            lat = e.latlng.lat
            lng = e.latlng.lng

            if( self.locationMarker != null )
                self.removeLayer(self.locationMarker)
            self.locationMarker = L.marker([lat, lng])
                    .addTo(self)
            self.locationMarker.bindPopup('<div id="map_popup" style="overflow:auto; width: 40px; height: 40px"><img src="/images/spiffygif_30x30.gif" alt="loading..."/></div>')
                    .openPopup()

            searchUrl = "/whathood-search?"+'lat='+lat+'&lng='+lng+'&format=json'
            $.ajax
                url: searchUrl,
                context: document.body,
                success: (data) ->
                    self.locationMarker.bindPopup(
                        getPopup(data,self.regionName)
                    ).openPopup()

        self.on('click', mapClickEventHandler )
