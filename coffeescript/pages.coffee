window = exports ? this
W = window.Whathood

W.user_polygon_view = () ->
  user_polygon_id = $("#user_polygon").attr 'data-id'
  throw new Error "user_polygon_id is not defined" unless user_polygon_id
  geoJsonUrl = "/api/v1/user-polygon/#{user_polygon_id}"
  map = new W.UserPolygonMap('map')
  map.addGeoJson( geoJsonUrl )
  map.addStreetLayer()

W.Page.user_polygon_id = () ->

W.user_polygon_page_center = () ->
    $node = $('input[name="user_polygon_id"]')
    up_id = $node.data('user_polygon_id')

    $.ajax
      url: "/api/v1/user-polygon/#{up_id}"
      success: (user_polygon) ->
        console.log user_polygon
        $form = $('.user_polygon_form')
        $name = $('input[name="neighborhood_name"]')
        $name.val user_polygon.neighborhood.name
        $name.text "adsfasdfasd"

        map = new W.UserPolygonMap 'map'
        map.addStreetLayer()
        map._add_geojson user_polygon
      error: (xhr,textStatus) ->
        alert xhr.response.msg

W.user_polygon_add = () ->

  map = new W.DrawMap('map')
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
        window.location.href = "/whathood/user-polygon/by-id/#{data.user_polygon_id}"
      error: (xhr,textStatus,errorThrown) ->
        alert "there was an error saving neighborhood: #{textStatus}"
    return false

W.neighborhood_show = () ->
  $map = $('#map')
  neighborhood_id = $map.data('neighborhood-id')
  throw new Error 'neighborhood_id may not be empty' unless neighborhood_id
  map = new W.Map 'map'
  map.addStreetLayer()
  map.addGeoJson W.Util.np_api_latest(neighborhood_id)

W.region_show = () ->

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

  $geo_search = $('div.leaflet-top.leaflet-center')

  # create a new region map
  map = new W.RegionMap('map')
  map.addStreetLayer()

  map.addGeoJson get_url(region_name,create_event)
  map.whathoodClick true

  l_geosearch = new W.GeoSearch
    provider: new L.GeoSearch.Provider.OpenStreetMap()
  .addTo(map)

  # we want its functionality, but not to see it
  $address_input = $('#leaflet-control-geosearch-qry')

  $leaflet_top_center = $('#map > div.leaflet-control-container > div.leaflet-top.leaflet-center')
  $leaflet_top_center.hide()

  # if address is in the query string, fill in the address search bar
  if QueryString.address
    $address_input.val replace_plus(QueryString.address)
    # fire off the geocoding
    l_geosearch._geosearch()
  else
    $("#address-modal").dialog
      title: "Find your neighborhood"
      draggable: false
      modal: true
      resizable: false
      buttons: [
        {
          text: "Close to Browse Map"
          click: () ->
            $(this).dialog 'close'
        }
      ]

  $btn = $('#current-location-btn')
  $btn.on 'click', (evt) ->
    W.Geo.browser_location (location) =>
      l_geosearch._my_showLocation
        X: location.coords.longitude
        Y: location.coords.latitude
      $('#address-modal').dialog 'close'
