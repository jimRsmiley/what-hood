window = exports ? this
Whathood = window.Whathood

class Whathood.UrlBuilder

  @user_neighborhood_by_point: (x,y) ->
    "/api/v1/user-neighborhood/x/#{x}/#{y}/"
