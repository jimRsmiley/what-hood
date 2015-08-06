window = exports ? this
Whathood = window.Whathood

class Whathood.PointElection

  @api_url: (x,y) ->
    Whathood.UrlBuilder.point_election x, y

  constructor: (args) ->

  @build: (x,y,cb) ->
    $.ajax
      url: @api_url x, y
      context: document.body
      success: (data) ->
        cb data
      fail: (xhr,textStatus) ->
        throw new Error "PointElection request failed"
