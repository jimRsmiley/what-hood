# Create a facade around heatmap layer
#
class Whathood.Map.HeatmapLayer extends HeatmapOverlay

  # return the heatmap config
  heatmap_cfg: () ->
    radius: .0017
    scaleRadius: true
    "maxOpacity": .65
    latField: 'y'
    lngField: 'x'
    valueField: 'weight'

  constructor: () ->
    super @heatmap_cfg()

  buildData: (neighborhood_id, callback) ->
    throw new Error "neighborhood_id must be defined" unless neighborhood_id
    $.ajax
      url: Whathood.UrlBuilder.heatmap_points_by_n_id neighborhood_id
      success: (heatmap_points) =>
        if heatmap_points.length > 0
          testData =
            max: 10
            data: heatmap_points
          @setData(testData)
        else
          console.log "no heatmap data for neighborhood id #{neighborhood_id}"
        callback @

  # redraw the map
  redraw: () ->
    @_draw()
