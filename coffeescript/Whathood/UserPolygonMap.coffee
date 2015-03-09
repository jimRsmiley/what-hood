root = exports ? this
Whathood = root.Whathood

Whathood.UserPolygonMap = Whathood.Map.extend

  _add_geojson: (geojson) ->
    console.log "geojson: ",geojson
    @geojsonLayer = new L.geoJson(geojson)

    console.log @
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
        someFeatures = [{
            "type": "Feature",
            "properties": {
                "name": "Coors Field",
                "show_on_map": true
            },
            "geometry": {
                "type": "Point",
                "coordinates": [-104.99404, 39.75621]
            }
        }, {
            "type": "Feature",
            "properties": {
                "name": "Busch Field",
                "show_on_map": false
            },
            "geometry": {
                "type": "Point",
                "coordinates": [-104.98404, 39.74621]
            }
        }]
        self.geojsonLayer = new L.geoJson(geojson)
        .addTo(self)
        self.fitBounds(self.geojsonLayer)
