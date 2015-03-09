window = exports ? this
Whathood = window.Whathood

class Whathood.Search
  @by_coordinates: (x,y,callback) ->
    url = "/api/v1/whathood/x/#{x}/y/#{y}"
    console.log url
    $.ajax
      url: url
      success: (data) ->
        callback data
      error: (err) ->
        throw new Error "lookup failed with url #{url}"
