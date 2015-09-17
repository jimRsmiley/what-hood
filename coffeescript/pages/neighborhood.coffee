window = exports ? this
W = window.Whathood

W.neighborhood =
  list: () ->
    dataTable = new Whathood.DataTable
      entity_name: 'neighborhood',
      # match the order up with the neighborhood json object
      column_names: [ 'id', 'createdAt', 'name', 'region'],
      div_id: "wh-datatable"
    dataTable.render()
