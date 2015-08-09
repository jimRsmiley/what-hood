root = exports ? this
Whathood = root.Whathood

Whathood.Map.NeighborhoodMap = Whathood.Map.extend

Whathood.Map.NeighborhoodMap.build = (css_id,neighborhood_id) ->
    $.ajax
      url: Whathood.UrlBuilder.heatmap_points_by_n_id neighborhood_id
      success: (mydata) =>
        console.log mydata
        testData = {
          max: 10
          data: mydata[0] }
        cfg = {
          "radius": 30,
          "maxOpacity": .8,
          latField: 'y',
          lngField: 'x',
          valueField: 'weight'
        }
        streetLayer = Whathood.Map.streetLayer()

        heatmapLayer = new HeatmapOverlay(cfg)
        map = new Whathood.Map css_id,
          center: new L.LatLng(39.962863586971,-75.126734904035)
          zoom: 14
          layers: [streetLayer,heatmapLayer]
        heatmapLayer.setData(testData)
        return map
