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

    _my_showLocation: (result) ->
      x = result.X
      y = result.Y
      this._showLocation result
      Whathood.Search.by_coordinates x, y, (data) =>
        popup_html = @popup_html data
        this._positionMarker.bindPopup(popup_html).openPopup()

    _geosearch: () ->
      queryBox = document.getElementById 'leaflet-control-geosearch-qry'
      @geosearch queryBox.value

    popup_html: (whathood_result) ->
      str = ""
      console.log whathood_result.whathood_result
      for neighborhood in whathood_result.response.consensus.neighborhoods
        str = "#{str}#{neighborhood.name}: #{neighborhood.votes}<br/>"
      console.log str
      str = "#{str}Disagree? <a href='/whathood/user-polygon/add'>Draw your own neighborhood</a> and we'll include merge it into the borders"
      return str
})

