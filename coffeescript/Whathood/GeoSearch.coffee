window = exports ? this
Whathood = window.Whathood

#
# extends L.Control.GeoSearch and override its action functions to catch the geocode result
# and popup a marker with content
#
class Whathood.GeoSearch extends L.Control.GeoSearch

    search_address: null

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
      Whathood.Search.by_coordinates x, y, (point_election_data) =>
        @_positionMarker.bindPopup(Whathood.RegionMap.getPopupHtml(point_election_data)).openPopup()

    geosearch: (value) ->
      console.log "processing #{value}"
      throw new Error("value must be defined") unless value
      #queryBox = document.getElementById 'leaflet-control-geosearch-qry'
      super value
