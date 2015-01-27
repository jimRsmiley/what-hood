root = exports ? this

Whathood = root.Whathood
Whathood.GeoSearch = L.Control.GeoSearch.extend({
    _processResults: (results) ->
      if (results.length > 0)
        this._map.fireEvent('geosearch_foundlocations', {Locations: results})
        this._showLocation(results[0])
      else
        this._printError(this._config.notFoundMessage)
})

$(document).ready ->
  url = window.location.pathname

  if /^\/$/.test url
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
      #$("#landing_dialog")?.close()

#    $("#landing_dialog_form").submit =>
#      address = get_search_address()
#      return false
