'use strict'

myApp = angular.module 'myApp', []

myApp.controller 'WhathoodRegionController', [ () ->
  W = window.Whathood
  get_region_name = () ->
    page_info = document.querySelector('#page-info')
    return page_info.dataset.regionName
  get_create_event = () ->
    page_info = document.querySelector('#page-info')
    return page_info.dataset.createEventId

  $('#current-location-btn').on 'click', (evt) ->
    W.Geo.browser_location (location) =>
      l_geosearch.showLocation
        X: location.coords.longitude
        Y: location.coords.latitude
      $('#address-modal').dialog 'close'

  region_name = get_region_name()
  create_event = get_create_event()

  $geo_search = $('div.leaflet-top.leaflet-center')

  # create a new region map
  map = new W.Map.RegionMap('map')
  map.addStreetLayer()
  map.whathoodClick true

  neighborhoods_url = W.UrlBuilder.neighborhood_border_by_region region_name, create_event
  map.addNeighborhoods neighborhoods_url, () =>
    # if address is in the query string, fill in the address search bar
    if QueryString.address
      $address_input.val replace_plus(QueryString.address)
      # fire off the geocoding
      l_geosearch.geosearch $address_input.val(), region_name
    else
      # only fit bounds if we're not popping up a marker
      map.fitBounds( map.geojsonLayer )

      # and pop up the address prompt
      $addressModal = $('#address-modal')
      $addressModal.dialog
        title: "Let's find your neighborhood"
        width: 600,
        autoOpen: false,
        modal: true,
        responsive: true
        # resizable and draggable cannot be false for the jquery responsive hack in whathood.js
        # resizable: false
        # draggable: false
        buttons: [
          {
            text: "Close to Browse Map"
            click: () ->
              $(this).dialog 'close'
          }
        ]
      $addressModal.find('#btn-submit').on 'click', ->
        $address_input.val( $('#enter-address').val() )
        l_geosearch.geosearch $address_input.val(), region_name
        $addressModal.dialog "close"
      $addressModal.dialog("open")

  l_geosearch = new W.GeoSearch
    provider: new L.GeoSearch.Provider.OpenStreetMap()
  .addTo(map)

  # this is what GeoSearch needs
  $address_input = $('#leaflet-control-geosearch-qry')

  $leaflet_top_center = $('#map > div.leaflet-control-container > div.leaflet-top.leaflet-center')
  $leaflet_top_center.hide()
]
