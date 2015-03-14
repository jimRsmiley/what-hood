window = exprots ? this
Whathood = window.Whathood

Whathood.user_polygon_view = () ->
  user_polygon_id = $("#user_polygon").attr 'data-id'
  throw new Error "user_polygon_id is not defined" unless user_polygon_id
  geoJsonUrl = "/api/v1/user-polygon/#{user_polygon_id}"
  map = new Whathood.UserPolygonMap('map')
  map.addGeoJson( geoJsonUrl )
  map.addStreetLayer()

Whathood.Page.user_polygon_id = () ->

Whathood.user_polygon_page_center = () ->
    $node = $('input[name="user_polygon_id"]')
    up_id = $node.data('user_polygon_id')
    console.log "user polygon id #{up_id}"

    $.ajax
      url: "/api/v1/user-polygon/#{up_id}"
      success: (user_polygon) ->
        $form = $('.user_polygon_form')
        $name = $('input[name="neighborhood_name"]')
        $name.val user_polygon.neighborhood.name
        $name.text "adsfasdfasd"

        map = new Whathood.UserPolygonMap 'map'
        map.addStreetLayer()
        map._add_geojson user_polygon
      error: (xhr,textStatus) ->
        alert xhr.response.msg

Whathood.user_polygon_add = () ->

  map = new Whathood.DrawMap('map')
  map.init()

  $('#add-polygon-form').on 'submit', (e) =>
    e.preventDefault()
    $form = $(e.target)

    polygon_json = map.getDrawnGeoJson()
    neighborhood_name =  $form.find("input[id='neighborhood_name']").val()

    unless polygon_json
      alert "polygon_json must be defined"
    unless neighborhood_name
      alert "neighborhood_name must be defined"
    url = '/whathood/user-polygon/add'
    data =
      polygon_json: map.getDrawnGeoJson()
      neighborhood_name: neighborhood_name
      region_name: 'Philadelphia'
    $.ajax
      type: 'POST'
      url: url
      data: data
      success: (data) ->
        console.log "addition successful"
        console.log data
        window.location.href = "/whathood/user-polygon/by-id/#{data.user_polygon_id}"
      error: (xhr,textStatus,errorThrown) ->
        alert "there was an error saving neighborhood: #{textStatus}"
    return false


Whathood.neighborhood_show = () ->
  $map = $('#map')
  neighborhood_id = $map.data('neighborhood-id')
  throw new Error 'neighborhood_id may not be empty' unless neighborhood_id
  console.log "neighborhood_id is #{neighborhood_id}"
  map = new Whathood.Map 'map'
  map.addStreetLayer()
  map.addGeoJson Whathood.Util.np_api_latest(neighborhood_id)

Whathood.region_show = () ->

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

  $geo_search = $('#leaflet-control-geosearch-qry')

  # create a new region map
  map = new Whathood.RegionMap('map')
  map.addStreetLayer()

  map.addGeoJson get_url(region_name,create_event)
  map.whathoodClick true

  $geosearch = new Whathood.GeoSearch
    provider: new L.GeoSearch.Provider.OpenStreetMap()
  .addTo(map)

  # if address is in the query string, fill in the address search bar
  if QueryString.address
    $geo_search.val replace_plus(QueryString.address)
    # fire off the geocoding
    geosearch._geosearch()
  else
    $("#address-modal").dialog
      draggable: false
      modal: true
      resizable: false

  $btn = $('#current-location-btn')
  $btn.on 'click', (evt) ->
    console.log $geosearch
    Whathood.Geo.browser_location (location) =>
      console.log "with location,",location
      $geosearch._my_showLocation
        X: location.coords.longitude
        Y: location.coords.latitude
      console.log $geosearch
      $('#address-modal').dialog 'close'
