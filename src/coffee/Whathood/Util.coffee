window = exports ? this
Whathood = window.Whathood

class Whathood.Util
  @np_api_latest: (neighborhood_id) ->
    "/api/v1/neighborhood-polygon?query_type=latest&neighborhood_id=#{neighborhood_id}"
