root = exports ? this
root.Whathood = {}

HeatmapOverlay.prototype.getBounds = () ->

  if (!this._southWest and !this._northEast)
    ys = (point.latlng.lat for point in this._data)
    xs = (point.latlng.lng for point in this._data)

    @_southWest = new Object()
    @_northEast = new Object()
    this._southWest.lat = Math.min.apply(null, ys)
    this._southWest.lng = Math.min.apply(null, xs)

    this._northEast.lat = Math.max.apply(null, ys);
    this._northEast.lng = Math.max.apply(null, xs);

  return new L.LatLngBounds(@_northEast, @_southWest)
