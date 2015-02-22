window = exports ? this
Whathood = window.Whathood

class Whathood.AddUserPolygonForm

  submitAddForm: () ->
    if( $('input[name="neighborhoodPolygon\\[neighborhood\\]\\[name\\]"]').val()  == '' )
        alert( 'you must enter a neighborhood name' )
    if( map.getDrawnGeoJson() == null )
        alert("you must draw a neighborhood to continue")
        return false

    console.log map.getDrawnGeoJson()
    return false
    hiddenJsonCssSelector = 'input[name=polygonGeoJson]'
    neighborhoodJson = $(hiddenJsonCssSelector).val()
    if( typeof neighborhoodJson == 'undefined' ) 
      alert( 'we did not get the neighborhood json' )
    $(hiddenJsonCssSelector).val( JSON.stringify(map.getDrawnGeoJson()) )
    console.log( "drawn-polygon-geojson: " + $(hiddenJsonCssSelector).val() )
    $('#AddNeighborhood').submit()
    return true


  loadNeighborhoodNames: () ->
    availableNeighborhoods = []

    $( "input[name=neighborhoodPolygon\\[neighborhood\\]\\[name\\]]" ).autocomplete
      source: availableNeighborhoods,
      response: (event, ui) ->
        # ui.content is the array that's about to be sent to the response callback.
        if (ui.content.length == 0)
          $("#no-match-neighborhood").html("You're about to add a brand new neighborhood name!  How exciting, just make sure no other name matches what you're trying to do")
        else
          $("#no-match-neighborhood").empty()


Whathood.Page.bind "/whathood/user-polygon/add", () ->

  map = new Whathood.DrawMap('map')
  map.init()

  $('#add-polygon-form').on 'submit', (e) =>
    e.preventDefault()
    $form = $(e.target)

    polygon_json = map.getDrawnGeoJson()
    neighborhood_name =  $form.find("input[id='neighborhood_name']").val()

    unless polygon_json
      alert "polygon_json must be defined"
    unless neighborhood_name
      alert "neighborhood_name must be defined"
    url = '/whathood/user-polygon/add'
    data =
      polygon_json: map.getDrawnGeoJson()
      neighborhood_name: neighborhood_name
      region_name: 'Philadelphia'
    $.ajax
      type: 'POST'
      url: url
      data: data
      success: (data) ->
        console.log "addition successful"
        console.log data
        #window.location.href = "/whathood/user-polygon/id/#{data.id}"
      error: (xhr,textStatus,errorThrown) ->
        alert "there was an error saving neighborhood: #{textStatus}"
    return false
