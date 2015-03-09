window = exprots ? this
Whathood = window.Whathood

Whathood.user_polygon_view = () ->
  user_polygon_id = $("#user_polygon").attr 'data-id'
  throw new Error "user_polygon_id is not defined" unless user_polygon_id
  geoJsonUrl = "/api/v1/user-polygon/#{user_polygon_id}"
  map = new Whathood.UserPolygonMap('map')
  map.addGeoJson( geoJsonUrl )
  map.addStreetLayer()
