var mapbox_map_id = 'jimrsmiley.k5eep890';

String.prototype.pluralize = function(count, plural)
{
  if (plural == null)
    plural = this + 's';

  return (count == 1 ? this : plural)
}

L.TileLayer.HeatMap.prototype.getBounds = function() {
     var self = this;
     return self._bounds;
}

L.GeoJSON.prototype.getCenter = function(){
    var pts = this._latlngs;

    var twicearea = 0;
    var p1, p2, f;
    var x = 0, y = 0;
    var nPts = pts.length;

    for(var i=0, j=nPts-1;i<nPts;j=i++) {
        p1=pts[i];
        p2=pts[j];
        twicearea+=p1.lat*p2.lng;
        twicearea-=p1.lng*p2.lat;

        f=p1.lat*p2.lng-p2.lat*p1.lng;

        x+=(p1.lat+p2.lat)*f;
        y+=(p1.lng+p2.lng)*f;
    }
    f=twicearea*3;
    return {lat: x/f,lng: y/f};
}


function getRandomColor() {
    var color = "#"+Math.floor(Math.random()*16777215).toString(16);
    return color;
}


function getURLParameter(name) {
    return decodeURI(
        (RegExp(name + '=' + '(.+?)(&|$)').exec(location.search)||[,null])[1]
    );
}
