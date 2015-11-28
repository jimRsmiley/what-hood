window = exports ? this
W = window.Whathood

class Whathood.DataTable

  constructor: (@args) ->
    @div_id = @args.div_id
    @entity_name = @args.entity_name
    @column_names = @args.column_names
    @$tableDiv = $("##{@div_id}")
    @columnDefs = @args.columnDefs
    $node = $("##{@div_id}")
    $node.html "<thead><tr></tr></thead>"
    $tr = $node.find('thead tr')
    for column in @args.columns
      $tr.append "<th>#{column.data}</th>"
    opts =
      "ordering": false,
      "processing": true,
      "serverSide": true,
      "ajax": "/api/v1/#{@entity_name}/data-tables"
    if @args.columnDefs
      opts.columnDefs = @args.columnDefs

    if @args.columns
      opts.columns = @args.columns

    @$tableDiv.DataTable opts

  render: () ->
