window = exports ? this
W = window.Whathood

class Whathood.DataTable

  constructor: (@args) ->
    @div_id = @args.div_id
    @entity_name = @args.entity_name
    @myvar = 0
    @column_names = @args.column_names

  render: () ->
    $node = $("##{@div_id}")
    $node.html "<thead><tr></tr></thead>"
    $tr = $node.find('thead tr')
    for id in @column_names
      $tr.append "<th>#{id}</th>"

    $("##{@div_id}").DataTable
      "ordering": false,
      "processing": true,
      "serverSide": true,
      "ajax": "/api/v1/#{@entity_name}/data-tables"

    # Add event listener for opening and closing details
    $('#data-table tbody').on 'click', 'td.details-control', (e) ->
      tr = $(e).closest('tr')
      row = table.row( tr )

      if row.child.isShown()
        # This row is already open - close it
        row.child.hide()
        tr.removeClass('shown')
      else
        # Open this row
        row.child( format(row.data()) ).show()
        tr.addClass('shown')
