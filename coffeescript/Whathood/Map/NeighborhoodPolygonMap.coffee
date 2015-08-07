root = exports ? this
Whathood = root.Whathood

Whathood.Map.NeighborhoodMap = Whathood.Map.extend

  add: (@args) ->
    neighborhood_id = @args.neighborhood_id
    throw new Error 'neighborhood_id may not be empty' unless neighborhood_id
    $.ajax
      url: "/api/v1/heat-map-points/neighborhood_id/#{neighborhood_id}"
      success: (mydata) =>
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
        baseLayer = L.tileLayer(
          'http://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png',{
            attribution: 'Map data &copy; <a href="http://openstreetmap.org">OpenStreetMap</a> contributors, <a href="http://creativecommons.org/licenses/by-sa/2.0/">CC-BY-SA</a>, Imagery © <a href="http://cloudmade.com">CloudMade</a>',
            maxZoom: 18
          }
        )
        heatmapLayer = new HeatmapOverlay(cfg)
        map = new Whathood.Map 'map', {
          center: new L.LatLng(39.962863586971,-75.126734904035)
          zoom: 14 
          layers: [ heatmapLayer ] }
        map.addStreetLayer()
        map.addGeoJson W.Util.np_api_latest(neighborhood_id)
        heatmapLayer.setData(testData)
