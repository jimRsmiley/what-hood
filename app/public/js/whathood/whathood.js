var mapbox_map_id = 'jimrsmiley.k5eep890';

String.prototype.pluralize = function(count, plural)
{
  if (plural == null)
    plural = this + 's';

  return (count == 1 ? this : plural)
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

var QueryString = function () {
  // This function is anonymous, is executed immediately and
  // the return value is assigned to QueryString!
  var query_string = {};
  var query = window.location.search.substring(1);
  var vars = query.split("&");
  for (var i=0;i<vars.length;i++) {
    var pair = vars[i].split("=");
    // If first entry with this name
    if (typeof query_string[pair[0]] === "undefined") {
      query_string[pair[0]] = pair[1];
    // If second entry with this name
    } else if (typeof query_string[pair[0]] === "string") {
      var arr = [ query_string[pair[0]], pair[1] ];
      query_string[pair[0]] = arr;
    // If third or later entry with this name
    } else {
      query_string[pair[0]].push(pair[1]);
    }
  }
    return query_string;
} ();

function replace_plus(str) {
  return str.replace(/\+/g,' ');
}
