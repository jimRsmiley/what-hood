NeighborhoodHeatMap = NewWhathoodMap.extend
    heatMapLayer: null
    data: null
    maxValue: 100

    addData: (data) ->
        this.data = data
        this.drawHeatmap(7)
        this.fitBounds( this.heatMapLayer )

    drawHeatmap: (radius) ->
        this.heatMapLayer = L.TileLayer.heatMap({
            radius: {value: 43, absolute:true},
            opacity: 0.70,
            gradient: {
                0.45: "rgb(0,0,255)",
                0.55: "rgb(0,255,255)",
                0.65: "rgb(0,255,0)",
                0.90: "yellow",
                1.0: "rgb(255,0,0)"
            }
        })
        this.heatMapLayer.setData( this.maxValue, this.data )
        this.heatMapLayer.addTo( this )
