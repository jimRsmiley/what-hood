window = exports ? this
Whathood = window.Whathood

class Whathood.Geo

  @browser_can_geo_locate: () ->
    return navigator.geolocation

  @browser_location: (cb) ->
    if @browser_can_geo_locate()
      navigator.geolocation.getCurrentPosition cb
