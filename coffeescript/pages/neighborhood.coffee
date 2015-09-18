window = exports ? this
W = window.Whathood

W.neighborhood =
  list: () ->
    dataTable = new Whathood.DataTable
      entity_name: 'neighborhood',
      # match the order up with the neighborhood json object
      column_names: [ 'id', 'createdAt', 'neighborhood_name', 'region'],
      div_id: "wh-datatable"
      columnDefs: [
        {
          render: (row_data, type, row) ->
            return "#{row_data} <a href=\"/neighborhood/id/#{row[0]}\">Show Map</a>"
          targets: 2
        }
      ]
    dataTable.render()
