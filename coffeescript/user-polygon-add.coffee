window = exports ? this
Whathood = window.Whathood

class Whathood.AddUserPolygonForm

  submitAddForm: () ->
    if( $('input[name="neighborhoodPolygon\\[neighborhood\\]\\[name\\]"]').val()  == '' )
        alert( 'you must enter a neighborhood name' )
    if( map.getDrawnGeoJson() == null )
        alert("you must draw a neighborhood to continue")
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

  map = new Whathood.DrawMap 'map'
  map.addStreetLayer()
  map.addLeafletDraw()

  loadNeighborhoodNames()
