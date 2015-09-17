window = exports ? this
W = window.Whathood

W.neighborhood =
  list: () ->
    console.log "pulling"
    $('#up_dataTable').DataTable( {
      "ordering": false,
      "processing": true,
      "serverSide": true,
      "ajax": '/api/v1/neighborhood/data-tables'
    })
