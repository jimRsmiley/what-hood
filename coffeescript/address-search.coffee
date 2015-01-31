root = exports ? this
Whathood = root.Whathood

Whathood.GeoSearch = L.Control.GeoSearch.extend({
    _processResults: (results) ->
      if (results.length > 0)
        console.log "found locations"
        console.log results
        x = results[0].X
        y = results[0].Y
        console.log "#{x}, #{y}"
        this._map.fireEvent('geosearch_foundlocations', {Locations: results})
        this._showLocation(results[0])
        new Whathood.Search().by_coordinates x, y, (data) ->
            console.log data
      else
        this._printError(this._config.notFoundMessage)
})

new Whathood.Page().bind "/", () ->
  get_search_address = () ->
    return $("#search_address").val()
  get_url = (region_name,create_event_id) ->
    return  "/whathood/neighborhood-polygon/show-region?region_name=#{region_name}&create_event_id=#{create_event_id}&format=json"

  map = new Whathood.RegionMap('map')
  map.addStreetLayer()
  page_info = document.querySelector('#page-info')
  create_event = page_info.dataset.createEventId
  region_name = page_info.dataset.regionName
  map.addGeoJson(get_url(region_name,create_event))

  geosearch = new Whathood.GeoSearch {
    provider: new L.GeoSearch.Provider.OpenStreetMap()
  }
  .addTo(map)

  $("#landing_dialog").dialog()
  $("#landing_close_button").click () ->
