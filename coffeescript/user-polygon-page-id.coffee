window = exports ? this
Whathood = window.Whathood

Whathood.Page.bind "/whathood/user-polygon/page-center", () ->
    $node = $('input[name="user_polygon_id"]')
    up_id = $node.data('user_polygon_id')
    console.log "user polygon id #{up_id}"

    $.ajax
      url: '/api/v1/user-polygon/#{up_id}'
      success: (data) ->
        console.log "data retreived",data
