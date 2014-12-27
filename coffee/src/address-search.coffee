$(document).ready ->
  url = window.location.pathname

  if /^\/$/.test url
    get_search_address = () ->
      return $("#search_address").val()
    get_url = (region_name,create_event_id) ->
      return  "/whathood/neighborhood-polygon/show-region?region_name=#{region_name}&create_event_id=#{create_event_id}&format=json"

    map = new RegionMap('map')
    map.addStreetLayer()

    page_info = document.querySelector('#page-info')
    create_event = page_info.dataset.createEventId
    region_name = page_info.dataset.regionName
    map.addGeoJson(get_url(region_name,create_event))

    new L.Control.GeoSearch {
      provider: new L.GeoSearch.Provider.Google()
    }
    .addTo(map)

    $("#landing_dialog").dialog()
    $("#landing_close_button").click () ->
      #$("#landing_dialog")?.close()

    $("#landing_dialog_form").submit =>
      address = get_search_address()
      return false
