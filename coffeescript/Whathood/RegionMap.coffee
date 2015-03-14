window = exports ? this
Whathood = window.Whathood

Whathood.RegionMap = Whathood.Map.extend

  _neighborhood_color: '5487b8'
  locationMarker: null

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
            self.fitBounds( self.geojsonLayer )

            if( ( typeof callback ) != 'undefined' )
                callback()
        failure: () ->
            throw new Error "WH.init(): something went wrong loading json source"
    .done () ->
      self.spin(false)

  getPopup: (whathood_result,regionName) ->

    neighborhoods = whathood_result.response.consensus.neighborhoods
    requestLat = whathood_result.request.lat
    requestLng = whathood_result.request.lng

    html = ''
    unless neighborhoods.length
      html = 'no neighborhoods found for this point'

    else
      for n in neighborhoods
        name  = n.name
        votes = n.votes
        html += votes + ' ' + "vote".pluralize(votes) + ' for ' + name + '<br/>'

        url = @getNeighborhoodBrowseUrl requestLat, requestLng

        if( typeof regionName != 'undefined' )
            url += '&region_name='+regionName
        html += '<a href="'+url+'">Browse these neighborhoods</a>'
    return html

  mapClickEventHandler: (e) ->
    lat = e.latlng.lat
    lng = e.latlng.lng

    if( @locationMarker != null )
      @removeLayer @locationMarker

    @locationMarker = L.marker([lat, lng])
      .addTo @
    @locationMarker.bindPopup('<div id="map_popup" style="overflow:auto; width: 40px; height: 40px"><img src="/images/spiffygif_30x30.gif" alt="loading..."/></div>')
      .openPopup()

    searchUrl = Whathood.Util.whathood_url lng,lat
    __self = @
    $.ajax
      url: searchUrl,
      context: document.body,
      success: (data) ->
        __self.locationMarker.bindPopup(
          __self.getPopup(data,__self.regionName)
        ).openPopup()

  getNeighborhoodBrowseUrl: (lat,lng) ->
    "/whathood/user-polygon/page-center/page/1/x/#{lat}/y/#{lng}"

  #
  #/*
  #* add the ability to click on the map, and have a whathood popup telling what
  #* neighborhoods it matches
  #*/
  whathoodClick: ( bool ) ->
    if( bool != true )
      return
    @on 'click', @mapClickEventHandler
