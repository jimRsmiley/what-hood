root = exports ? this
Whathood = root.Whathood

Whathood.UserPolygonMap = Whathood.Map.extend

  addGeoJson: (url) ->
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
