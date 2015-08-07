window = exports ? this
Whathood = window.Whathood

class Whathood.UrlBuilder

  @user_neighborhood_by_point: (x, y, page_num) ->
    page_num = 0 unless page_num
    "/whathood/user-neighborhood/page-center/page/#{page_num}/x/#{x}/y/#{y}/"

  @point_election: (x,y) ->
    "/api/v1/point-election/x/#{x}/y/#{y}/"

  @neighborhood_by_name: (region_name,neighborhood_name) ->
    "/#{region_name}/#{neighborhood_name}"
