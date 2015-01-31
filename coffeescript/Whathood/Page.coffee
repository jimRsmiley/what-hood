window = exports ? this
Whathood = window.Whathood

class Whathood.Page
  instance = null
  constructor: () ->
    if instance
      return instance
    else
      instance = this
  bind : (url,callback) ->
    $(document).ready ->
      if window.location.pathname == url
        callback()
