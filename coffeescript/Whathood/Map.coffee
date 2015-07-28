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

  addStreetLayer : ->
    streetLayer = L.tileLayer 'https://{s}.tiles.mapbox.com/v3/{id}/{z}/{x}/{y}.png',
      maxZoom: 18
      attribution: @attribution
      id: mapbox_map_id

    streetLayer.addTo @
    @layerGroup().addLayer streetLayer
    return

  addGeoJson: ( url, callback ) ->
    $.ajax
      url: url
      context: this
      success: (geojson) ->
        @geojsonLayer = new L.geoJson geojson
        .addTo(@)
        @layerGroup().addLayer( @geojsonLayer )
        @fitBounds( @geojsonLayer )

        if( ( typeof callback ) != 'undefined' )
          callback()
      failure: () ->
        throw new Error "WH.init(): something went wrong loading json source"
    .done () ->
      @spin(false)
