root = exports ? this
Whathood = root.Whathood

Whathood.UserPolygonMap = Whathood.Map.extend

  _add_geojson: (geojson) ->
    @geojsonLayer = new L.geoJson(geojson)

    @geojsonLayer.addTo(@)
    @fitBounds(@geojsonLayer)

  addGeoJson: (args) ->

    if args.geojson
      @_add_geojson args.geojson
      return

    url = args
    self = this
    $.ajax
      url: url,
      success: (geojson) =>
        self.geojsonLayer = new L.geoJson(geojson)
        .addTo(self)
        self.fitBounds(self.geojsonLayer)
