root = exports ? this

Whathood = root.Whathood

Whathood.RegionMap = Whathood.Map.extend
  _markerCluster : null,
  addContentiousPoints : (createEventId, callback ) =>
    self = this
    url = '/whathood/contentious-point/by-create-event-id?format=heatmapJsData&create_event_id='+createEventId
    $.ajax
      url: url,
      success: (pointData) ->
        self._markerCluster = new L.MarkerClusterGroup()
        count = 0
        pointData.forEach ( point, index, array ) ->
          if( ( index % 10 ) == 0 )
            self._markerCluster.addLayer( new L.Marker([point.lat, point.lon] ) )
            count++

            self.addLayer(self._markerCluster )

            if( ( typeof callback ) != 'undefined' )
              callback()
            error: () ->
              alert('unable to retreive contentious points')

