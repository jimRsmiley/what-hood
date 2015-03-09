window = exports ? this
Whathood = window.Whathood

class Whathood.Util
  @whathood_url: (x,y) ->
    "/api/v1/whathood/x/#{x}/y/#{y}"
