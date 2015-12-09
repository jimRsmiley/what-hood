window = exports ? this
Whathood = window.Whathood

class Whathood.PointElection

  constructor: (@args) ->
    
  @api_url: (x,y) ->
    Whathood.UrlBuilder.point_election x, y

  toHtml: () ->
    data =
      total_votes: @totalVotes()
      is_tie: @isTie()
    html = "" 
    for key, value of data
      html = html + "<div>"+key+" : "+ value + "</div>"
    return '<div>'+ html + '</div>'

  isTie: () -> @args.is_tie

  totalVotes: () ->
    @args.total_votes

  point: () ->
    @args.point

  @build: (x,y,cb) ->
    $.ajax
      url: @api_url x, y
      context: document.body
      success: (data) ->
        cb data
      error: (xhr,textStatus) ->
        cb()
