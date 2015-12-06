class Whathood.Map.BaseControl extends L.Control

  contructor: () ->
    info = L.control()

  onAdd: (map) ->
        this._div = L.DomUtil.create('div', 'info')
        this.update()
        return this._div

  update: (props) ->
      if props
        this._div.innerHTML = '<h4>What Hood Neighborhoods</h4>' +
          '<b>' + props.name + '</b><br />'
          +'<b>Number of users who contributed to these borders:</b>'+props.num_user_polygons+'<br/>'
          +'<br/>'
          +'<a href="/Philadelphia/'+props.name+'">Go to ' + name + " identity heatmap</a>"
      else
        this._div.innerHTML = '<h4>What Hood Neighborhoods</h4>' +
          'Click a neighborhood'

