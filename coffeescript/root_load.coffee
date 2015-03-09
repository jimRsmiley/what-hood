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
      str = ""
      console.log whathood_result.whathood_result
      for neighborhood in whathood_result.response.consensus.neighborhoods
        str = "#{str}#{neighborhood.name}: #{neighborhood.votes}<br/>"
      console.log str
      str = "#{str}Disagree? <a href='/whathood/user-polygon/add'>Draw your own neighborhood</a> and we'll include merge it into the borders"
      return str
})

# on '/'
Whathood.root_load = () ->

  get_region_name = () ->
    page_info = document.querySelector('#page-info')
    return page_info.dataset.regionName
  get_create_event = () ->
    page_info = document.querySelector('#page-info')
    return page_info.dataset.createEventId
  get_url = (region_name,create_event_id) ->
    return  "/whathood/neighborhood-polygon/show-region?region_name=#{region_name}&format=json"

  region_name = get_region_name()
  create_event = get_create_event()

  # create a new region map
  map = new Whathood.RegionMap('map')
  map.addStreetLayer()

  map.addGeoJson get_url(region_name,create_event)
  map.whathoodClick true

  geosearch = new Whathood.GeoSearch
    provider: new L.GeoSearch.Provider.OpenStreetMap()
  .addTo(map)

  # if address is in the query string, fill in the address search bar
  if QueryString.address
    $('#leaflet-control-geosearch-qry').val replace_plus(QueryString.address)
    # fire off the geocoding
    geosearch._geosearch()
