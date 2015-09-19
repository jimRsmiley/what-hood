window = exports ? this
W = window.Whathood

W.pages = {} unless W.pages

console.log "this is happening"
W.pages.queue =
  dataTables: () ->
    dataTable = new Whathood.DataTable
      entity_name: "queue",
      columns: [
          {data: 'id'},
          {data: 'message'},
          {data: 'status'},
          {data: 'status_string'}
      ]
      div_id: "wh-datatables-queue"
    dataTable.render()
