window = exports ? this
W = window.Whathood

W.neighborhood_polygon_show = () ->
  $map = $('#map')
  neighborhood_id = $map.data('neighborhoodId')
  map = W.Map.NeighborhoodMap.build('map', neighborhood_id)
