window = exports ? this
W = window.Whathood

W.neighborhood =
  list: () ->
    dataTable = new Whathood.DataTable
      debug: true
      entity_name: 'neighborhood',
      div_id: "wh-datatable"
      columns: [
          {data: 'id'}
          {data: 'date_time_added'}
          {data: 'name'}
      ]
      columnDefs: [
        {
          render: (row_data, type, row) ->
            return "#{row_data} <a href=\"/neighborhood/id/#{row.id}\">Show Map</a>"
          targets: 2
        }
      ]
    dataTable.render()
