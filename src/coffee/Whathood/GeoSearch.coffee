window = exports ? this
Whathood = window.Whathood

#
# extends L.Control.GeoSearch and override its action functions to catch the geocode result
# and popup a marker with content
#
class Whathood.GeoSearch extends L.Control.GeoSearch

    search_address: null

    # overload parent function
    _processResults: (results) ->
      if (results.length > 0)
        this._map.fireEvent('geosearch_foundlocations', {Locations: results})
        this._showLocation results[0]
      else
        this._printError(this._config.notFoundMessage)
        throw new Error "did not find any results for address"

    # sugar
    showLocation: (result) ->
        @_showLocation result

    # overloading parent function
    _showLocation: (result) ->
      x = result.X
      y = result.Y
      super result
      Whathood.Search.by_coordinates x, y, (point_election_data) =>
        @_positionMarker.bindPopup(Whathood.Map.RegionMap.getPopupHtml(point_election_data)).openPopup()

    geosearch: (value, region_name) ->
      throw new Error("value must be defined") unless value
      throw new Error("region_name must be defined") unless region_name
      value = value + ", #{region_name}"
      @search_address = value
      super value
