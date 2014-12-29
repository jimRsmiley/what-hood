(function() {
  var root;

  root = typeof exports !== "undefined" && exports !== null ? exports : this;

  root.Whathood = {};

}).call(this);

(function() {
  var Whathood, root;

  root = typeof exports !== "undefined" && exports !== null ? exports : this;

  Whathood = root.Whathood;

  Whathood.GeoSearch = L.Control.GeoSearch.extend({
    _processResults: function(results) {
      if (results.length > 0) {
        this._map.fireEvent('geosearch_foundlocations', {
          Locations: results
        });
        return this._showLocation(results[0]);
      } else {
        return this._printError(this._config.notFoundMessage);
      }
    }
  });

  $(document).ready(function() {
    var create_event, geosearch, get_search_address, get_url, map, page_info, region_name, url;
    url = window.location.pathname;
    if (/^\/$/.test(url)) {
      get_search_address = function() {
        return $("#search_address").val();
      };
      get_url = function(region_name, create_event_id) {
        return "/whathood/neighborhood-polygon/show-region?region_name=" + region_name + "&create_event_id=" + create_event_id + "&format=json";
      };
      map = new RegionMap('map');
      map.addStreetLayer();
      page_info = document.querySelector('#page-info');
      create_event = page_info.dataset.createEventId;
      region_name = page_info.dataset.regionName;
      map.addGeoJson(get_url(region_name, create_event));
      geosearch = new Whathood.GeoSearch({
        provider: new L.GeoSearch.Provider.OpenStreetMap()
      }).addTo(map);
      $("#landing_dialog").dialog();
      return $("#landing_close_button").click(function() {});
    }
  });

}).call(this);
