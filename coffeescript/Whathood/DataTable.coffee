window = exports ? this
W = window.Whathood

class Whathood.DataTable

  constructor: (@args) ->
    @div_id = @args.div_id
    @entity_name = @args.entity_name
    @myvar = 0
    @column_names = @args.column_names
    @$tableDiv = $("##{@div_id}")
    @columnDefs = @args.columnDefs
  render: () ->
    $node = $("##{@div_id}")
    $node.html "<thead><tr></tr></thead>"
    $tr = $node.find('thead tr')
    for id in @column_names
      $tr.append "<th>#{id}</th>"

    opts =
      "ordering": false,
      "processing": true,
      "serverSide": true,
      "ajax": "/api/v1/#{@entity_name}/data-tables"

    if @columnDefs
      opts.columnDefs = @columnDefs

    console.log opts
    @$tableDiv.DataTable opts
