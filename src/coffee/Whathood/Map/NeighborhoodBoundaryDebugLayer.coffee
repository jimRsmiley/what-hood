class NeighborhoodBoundaryDebugLayer

  @build: () ->
        if false
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

