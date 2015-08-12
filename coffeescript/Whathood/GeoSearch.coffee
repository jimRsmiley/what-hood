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
        console.log data
        template_manager = new Whathood.TemplateManager()
        template_manager.load_template "whathood_click_result", "whathood_click_result", =>
          markup = template_manager.transform "whathood_click_result", data
          @_positionMarker.bindPopup(markup).openPopup()
          return markup

    _geosearch: () ->
      queryBox = document.getElementById 'leaflet-control-geosearch-qry'
      @geosearch queryBox.value
})

