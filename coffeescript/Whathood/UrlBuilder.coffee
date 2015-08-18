window = exports ? this
Whathood = window.Whathood

class Whathood.UrlBuilder

  @user_neighborhood_by_point: (x, y, page_num) ->
    page_num = 0 unless page_num
    "/user-neighborhood/page-center/page/#{page_num}/x/#{x}/y/#{y}/"

  @getNeighborhoodBrowseUrl: (lat,lng) ->
    "/user-neighborhood/page-center/page/1/x/#{lat}/y/#{lng}"
  @userBorderAddPost: () ->
    "/user-neighborhood/add-post"

  @api_root: ->
    "/api/v1"

  @point_election: (x,y) ->
    "#{@api_root()}/point-election/x/#{x}/y/#{y}/"

  @neighborhood_border_by_region: (region_name) ->
    "#{@api_root()}/neighborhood-border/region/#{region_name}"

  @neighborhood_border_by_id: (neighborhood_id) ->
    "#{@api_root()}/neighborhood-border/#{neighborhood_id}"

  @heatmap_points_by_n_id: (neighborhood_id) ->
    "#{@api_root()}/heatmap-points/neighborhood_id/#{neighborhood_id}"

  @neighborhood_by_name: (region_name,neighborhood_name) ->
    "/#{region_name}/#{neighborhood_name}"
