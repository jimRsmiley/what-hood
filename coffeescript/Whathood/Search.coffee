window = exports ? this
Whathood = window.Whathood

class Whathood.Search
  @by_coordinates: (x,y,callback) ->
    url = "/whathood/search/by-position?x=#{x}&y=#{y}"
    console.log url
    $.ajax
      url: url
      success: (data) ->
        callback data
      error: (err) ->
        alert "lookup failed"
