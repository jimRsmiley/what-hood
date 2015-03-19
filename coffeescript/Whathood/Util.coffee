window = exports ? this
Whathood = window.Whathood

class Whathood.Util
  @np_api_latest: (id) ->
    "/api/v1/neighborhood-polygon?neighborhood_latest_polygon_id=#{id}"
