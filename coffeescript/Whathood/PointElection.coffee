window = exports ? this
Whathood = window.Whathood

class Whathood.PointElection

  @api_url: (x,y) ->
    "/api/v1/whathood/x/#{x}/y/#{y}"

  constructor: (args) ->
    @winners        = args.winners
    @region         = args.region
    @neighborhoods  = args.neighborhoods
    @total_votes    = args.total_votes
    @point          = args.point

  @build: (x,y,cb) ->
    $.ajax
      url: @api_url x, y
      context: document.body
      success: (data) ->
        pointElection = new Whathood.PointElection data
        cb pointElection
      fail: (xhr,textStatus) ->
        throw new Error "PointElection request failed"
