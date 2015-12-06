class Whathood.Map.RegionMapControl extends Whathood.Map.BaseControl

  update: (props) ->
    if props
      @_div.innerHTML = '<h4>What Hood Neighborhoods</h4>' +
        '<b>' + props.name + '</b><br />'
        +'<b>Number of users who contributed to these borders:</b>'+props.num_user_polygons+'<br/>'
        +'<br/>'
        +'<a href="/Philadelphia/'+props.name+'">Go to ' + name + " identity heatmap</a>"
    else
      @_div.innerHTML = '<h4>What Hood Neighborhoods</h4>' +
        'Click a neighborhood'
