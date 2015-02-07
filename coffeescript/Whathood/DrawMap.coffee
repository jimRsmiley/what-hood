window = exports ? this

Whathood = window.Whathood

Whathood.DrawMap = Whathood.Map.extend
    drawnItems : null,
    neighborhoodLayer : null,

    init: ->
      @addStreetLayer()
      @addLeafletDraw()
      @addInfoWindow()

    addInfoWindow: ->
      info = L.control
        position:'topright'
      info.onAdd = (map) ->
        div = L.DomUtil.create 'div','command'

        div.innerHTML = '
          <div>Begin Drawing the neighborhood borders by clicking the pentagon button</div>
          <form id="add-polygon-form">
            <input id="neighborhood_name" type="text"/>
            <button>Save Neighborhood</button>
          </form>'
        return div
      info.addTo @

    addLeafletDraw : ->
        this.drawnItems = new L.FeatureGroup()
        this.addLayer this.drawnItems
        this.addControl this.getDrawControl()

        this.on 'draw:created', (e) ->
            type = e.layerType
            layer = e.layer

            if (type == 'marker')
                layer.bindPopup('A popup!')

            this.drawnItems.addLayer(layer)

            this.neighborhoodLayer = layer

        this.on 'draw:edited',  (e) ->
            layers = e.layers
            countOfEditedLayers = 0
            layers.eachLayer (layer) ->
              countOfEditedLayers++

    getDrawControl : ->

        editableLayers = null
        if( @neighborhoodLayer != null )
          editableLayers = new L.FeatureGroup([@neighborhoodLayer])
        else
          editableLayers = new L.FeatureGroup()

        options = {
            draw: {
                position: 'topleft',
                polygon: {
                    title: 'Draw a neighborhood!',
                    allowIntersection: false,
                    drawError: {
                        color: '#b00b00',
                        timeout: 1000
                    },
                    shapeOptions: {
                        color: '#54564b'
                    }
                },
                circle: false,
                polyline: false,
                rectangle: false,
                marker: false
            },
            edit: {
                featureGroup: editableLayers, #REQUIRED!!
            }
        }
        return new L.Control.Draw(options)
    getDrawnGeoJson: ->
        return @neighborhoodLayer?.toGeoJSON()
