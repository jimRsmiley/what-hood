window = exports ? this
Whathood = window.Whathood

Whathood.RegionMap = Whathood.Map.extend

  _neighborhood_color: '5487b8'
  locationMarker: null

  addNeighborhoods: ( url, callback ) ->
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

            onEachFeature = (feature,layer) =>
                layer.on
                  mouseover: highlightFeature
                  click: (e) =>
                    @mapClickEventHandler e
                  mouseout: resetHighlight

            self.geojsonLayer = new L.geoJson(geojson, {
                style: style,
                id: 'geojson',
                onEachFeature: onEachFeature
            }).addTo(self)
            self.layerGroup().addLayer( self.geojsonLayer )

            if( ( typeof callback ) != 'undefined' )
                callback()
        failure: () ->
            throw new Error "WH.init(): something went wrong loading json source"
    .done () ->
      self.spin(false)

  # when a user clicks on the map, get the lat,lng and send a request for a
  # point election, then display it
  mapClickEventHandler: (e) ->
    lat = e.latlng.lat
    lng = e.latlng.lng

    if( @locationMarker != null )
      @removeLayer @locationMarker

    @locationMarker = L.marker([lat, lng])
      .addTo @
    @locationMarker.bindPopup('<div id="map_popup" style="overflow:auto; width: 40px; height: 40px"><img src="/images/spiffygif_30x30.gif" alt="loading..."/></div>')
      .openPopup()

    # send a point election api request and popup the marker with it's result
    pointElection = Whathood.PointElection.build lng, lat, (pointElection) =>
      @locationMarker.bindPopup(
        Whathood.RegionMap.getPopupHtml pointElection
      ).openPopup()

  #
  #/*
  #* add the ability to click on the map, and have a whathood popup telling what
  #* neighborhoods it matches
  #*/
  whathoodClick: ( bool ) ->
    if( bool != true )
      return
    @on 'click', @mapClickEventHandler

Whathood.RegionMap.getPopupHtml = (point_election_data) ->
    url = Whathood.UrlBuilder.user_neighborhood_by_point(point_election_data.point.x, point_election_data.point.y)
    return React.renderToString( React.createElement(PointElection, {browse_url: url, point_election: point_election_data}), document.getElementById('reactpopup') )
