class Whathood.Map.BaseControl extends L.Control

  onAdd: (map) ->
    @_div = L.DomUtil.create('div', 'info')
    @update()
    return @_div

  header: ->
    '<h2>Whathood is this?</h2>'
