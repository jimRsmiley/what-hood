window = exports ? this
Whathood = window.Whathood

Whathood.Map = L.Map.extend


  _layerGroup: null,
  _geojsonTileLayer : null
  attribution: 'Map data &copy; <a href="http://openstreetmap.org">OpenStreetMap</a> contributors | <a href="http://creativecommons.org/licenses/by-sa/2.0/">CC-BY-SA</a> | Imagery © <a href="http://mapbox.com">Mapbox</a> '

  layerGroup: ->
    if (@_layerGroup == null)
      @_layerGroup = new L.LayerGroup()
    return @_layerGroup

  centerOnRegion: () ->
    this.setView([39.9505, -75.148], 12)


  addMarker: (x, y, popupHtml) ->
    L.marker([y, x]).addTo(@)
      .bindPopup(popupHtml+"")

  addStreetLayer: ->
    streetLayer = Whathood.Map.streetLayer()
    streetLayer.addTo @
    @layerGroup().addLayer streetLayer

  addGeoJson: ( url, callback ) ->
    $.ajax
      url: url
      context: this
      success: (geojson) ->

        if geojson.length > 0
          @geojsonLayer = new L.geoJson geojson
          .addTo(@)
          @layerGroup().addLayer( @geojsonLayer )
          @fitBounds( @geojsonLayer )

        if typeof( callback ) != 'undefined'
          callback(geojson)
      failure: () ->
        throw new Error "WH.init(): something went wrong loading json source"
    .done () ->
      @spin(false)

Whathood.Map.streetLayer = ->
    streetLayer = L.tileLayer 'https://{s}.tiles.mapbox.com/v3/{id}/{z}/{x}/{y}.png',
      maxZoom: 18
      attribution: @attribution
      id: mapbox_map_id
    return streetLayer

