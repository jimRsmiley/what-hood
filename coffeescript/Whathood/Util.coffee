window = exports ? this
Whathood = window.Whathood

class Whathood.Util
  @whathood_url: (x,y) ->
    "/api/v1/whathood/x/#{x}/y/#{y}"
  @np_api_latest: (id) ->
    "/api/v1/neighborhood-polygon?neighborhood_latest_polygon_id=#{id}"
