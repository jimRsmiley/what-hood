# the region map
#
Whathood.Map.RegionMap = Whathood.Map.extend

  _neighborhood_color: '5487b8'
  locationMarker: null

  addNeighborhoods: ( url, callback ) ->
    $.ajax
      url: url,
      success: (geojson) =>
        @addRegionGeoJson geojson
        if ( typeof callback ) != 'undefined'
          callback()
      failure: () ->
        throw new Error "WH.init(): something went wrong loading json source"

  addRegionGeoJson: (geojson) ->
    self = @
    # control that shows state info on hover
    info = new Whathood.Map.RegionMapControl()
    info.addTo @

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

    updateInfo = (e) =>
      layer = e.target
      info.update(layer.feature.properties)

    resetHighlight = (e) =>
        @geojsonLayer.resetStyle(e.target)

    onEachFeature = (feature,layer) =>
        layer.on
          mouseover: highlightFeature
          click: (e) =>
            @mapClickEventHandler e
          mouseout: resetHighlight

    @geojsonLayer = new L.geoJson(geojson, {
        style: style,
        id: 'geojson',
        onEachFeature: onEachFeature
    }).addTo @
    @layerGroup().addLayer @geojsonLayer

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
        Whathood.Map.RegionMap.getPopupHtml pointElection
      ).openPopup()

  # add the ability to click on the map, and have a whathood popup telling what
  # neighborhoods it matches
  # 
  whathoodClick: ( bool ) ->
    if( bool != true )
      return
    @on 'click', @mapClickEventHandler

Whathood.Map.RegionMap.getPopupHtml = (point_election_data) ->
    url = Whathood.UrlBuilder.user_neighborhood_by_point(point_election_data.point.x, point_election_data.point.y)
    return React.renderToString( React.createElement(PointElection, {browse_url: url, point_election: point_election_data}), document.getElementById('reactpopup') )
