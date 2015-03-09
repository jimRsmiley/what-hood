window = exports ? this
Whathood = window.Whathood

Whathood.load_user_polygon_page_center = () ->
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
