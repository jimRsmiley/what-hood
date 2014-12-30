window = exports ? this

Whathood = window.Whathood

Whathood.DrawMap = Whathood.Map.extend
    drawnItems : null,
    neighborhoodLayer : null,

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
        if( this.neighborhoodLayer != null )
          editableLayers = new L.FeatureGroup([this.neighborhoodLayer])
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
        return this.neighborhoodLayer.toGeoJSON()
