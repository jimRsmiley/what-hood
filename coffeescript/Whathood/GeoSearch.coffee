window = exports ? this
Whathood = window.Whathood

#
# extends L.Control.GeoSearch and override its action functions to catch the geocode result
# and popup a marker with content
#
Whathood.GeoSearch = L.Control.GeoSearch.extend({

    _processResults: (results) ->
      if (results.length > 0)
        this._map.fireEvent('geosearch_foundlocations', {Locations: results})
        this._my_showLocation results[0]
      else
        this._printError(this._config.notFoundMessage)
        throw new Error "did not find any results for address"

    _my_showLocation: (result) ->
      x = result.X
      y = result.Y
      this._showLocation result
      Whathood.Search.by_coordinates x, y, (data) =>
        html = React.renderToString( React.createElement(WhathoodClickResult, data), document.getElementById('reactpopup') )
        @_positionMarker.bindPopup(html).openPopup()
    _geosearch: () ->
      queryBox = document.getElementById 'leaflet-control-geosearch-qry'
      @geosearch queryBox.value
})

