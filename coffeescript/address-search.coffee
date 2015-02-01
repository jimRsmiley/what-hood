window = exports ? this
Whathood = window.Whathood

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
      queryBox = document.getElementById('leaflet-control-geosearch-qry')
      @geosearch(queryBox.value)
    popup_html: (whathood_result) ->
      console.log "in popup_html"
      str = ""
      console.log whathood_result.whathood_result
      for neighborhood in whathood_result.whathood_result.response.consensus.neighborhoods
        str = "#{str}#{neighborhood.name}: #{neighborhood.votes}<br/>"
      console.log whathood_result
      console.log str
      return str
})

Whathood.Page.bind "/", () ->

  get_region_name = () ->
    page_info = document.querySelector('#page-info')
    return page_info.dataset.regionName
  get_create_event = () ->
    page_info = document.querySelector('#page-info')
    return page_info.dataset.createEventId
  get_url = (region_name,create_event_id) ->
    return  "/whathood/neighborhood-polygon/show-region?region_name=#{region_name}&create_event_id=#{create_event_id}&format=json"

  # create a new region map
  map = new Whathood.RegionMap('map')
  map.addStreetLayer()
  region_name = get_region_name()
  create_event = get_create_event()
  map.addGeoJson(get_url(region_name,create_event))

  geosearch = new Whathood.GeoSearch {
    provider: new L.GeoSearch.Provider.OpenStreetMap()
  }
  .addTo(map)

  # if address is in the query string, fill in the address search bar
  if QueryString.address
    $('#leaflet-control-geosearch-qry').val replace_plus(QueryString.address)
    # fire off the geocoding
    geosearch._geosearch()
