window = exports ? this
Whathood = window.Whathood

class Whathood.Search
  @by_coordinates: (x,y,callback) ->
    url = "/api/v1/point-election/x/#{x}/y/#{y}"
    $.ajax
      url: url
      success: (data) ->
        callback data
      error: (err) ->
        throw new Error "lookup failed with url #{url}"
