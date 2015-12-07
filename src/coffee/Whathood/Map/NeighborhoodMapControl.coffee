class Whathood.Map.NeighborhoodMapControl extends Whathood.Map.BaseControl

  update: (props) ->
    @_div.innerHTML = @header()
    if props
      @_div.innerHTML += '<p><b>' + props.neighborhoodName + '</b><br />'+'<b>Number of users who contributed to these borders:</b>'+props.numUserPolygons+'<br/></p>'
