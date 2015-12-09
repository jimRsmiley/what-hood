root = exports ? this
root.Whathood = {}

# from https://danlimerick.wordpress.com/2014/01/18/how-to-catch-javascript-errors-with-window-onerror-even-on-chrome-and-firefox/
#window.onerror = function (errorMsg, url, lineNumber, column, errorObj) {
#    alert('Error: ' + errorMsg + ' Script: ' + url + ' Line: ' + lineNumber
#    + ' Column: ' + column + ' StackTrace: ' +  errorObj);
#}

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
