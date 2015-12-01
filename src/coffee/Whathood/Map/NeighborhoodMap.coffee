# a Neighborhood map is a map displays:
#
# * the boundary for the neighborhood
# * the heatmap for the neighborhood
#
# usage:
# map = NeighborhoodMap.build('#neighborhood_map', neighborhood_id)
#
class Whathood.Map.NeighborhoodMap extends Whathood.Map


  @heatmap_cfg : () ->
    "radius": 8,
    "maxOpacity": .65,
    latField: 'y',
    lngField: 'x',
    valueField: 'weight'

  # need to build the map because the heatmapLayer needs to go into the map constructor
  @build: (css_id,neighborhood_id) ->
    throw new Error "neighborhood_id must be defined" unless neighborhood_id

    region_name         = "Philadelphia"
    neighborhood_name   = "Rittenhouse"
    grid_resolution     = 0.0009
    $.ajax
      url: Whathood.UrlBuilder.heatmap_points_by_n_id neighborhood_id
      success: (heatmap_points) =>

        streetLayer = Whathood.Map.streetLayer()
        heatmapLayer = new HeatmapOverlay(Whathood.Map.NeighborhoodMap.heatmap_cfg())
        map = new Whathood.Map.NeighborhoodMap css_id,
          center: new L.LatLng(39.962863586971,-75.126734904035)
          zoom: 14
          layers: [streetLayer,heatmapLayer]
        if heatmap_points.length > 0
          testData =
            max: 10
            data: heatmap_points

        url = Whathood.UrlBuilder.neighborhood_border_by_id(neighborhood_id)
        map.addNeighborhoodBorder url, (geojson) ->
          heatmapLayer.setData(testData)
          map.fitBounds(heatmapLayer)

          # add the neighborhood boundary
          new L.geoJson(geojson).addTo(map)

        if true
          get_args = 
              neighborhood: neighborhood_name
              region: region_name
              grid_res: grid_resolution
          $.ajax
            url: '/api/v1/neighborhood-border/debug-build/Philadelphia/Rittenhouse/0.0015'
            success: (data) ->
              console.log "got data for build debug"
              total_points = data.all_point_elections.length
              neib_wins = 0
              RedIcon = new L.Icon({
                iconUrl: '/images/marker-icon-red.png'
                iconAnchor: new L.Point(32, 32)
              })
              for pe_data in data.all_point_elections
                point_election = new Whathood.PointElection pe_data 
                point = point_election.point()
                
                if point_election.isTie()
                  if point_election.totalVotes() > 2
                    console.log pe_data
                  ++neib_wins
                  marker = L.marker([point.y, point.x], {icon: RedIcon}).addTo(map)
                else
                  marker = L.marker([point.y, point.x]).addTo(map)
                marker.bindPopup point_election.toHtml()

        return map

  # sugar
  addNeighborhoodBorder: (url, cb) ->
    @addGeoJson url, cb
