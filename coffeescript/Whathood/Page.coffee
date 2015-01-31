window = exports ? this
Whathood = window.Whathood

class Whathood.Page
  instance = null
  constructor: () ->
    if instance
      return instance
    else
      instance = this
      return instance
  bind : (url,callback) ->
    console.log "binding on #{url}"
    $(document).ready ->
      if window.location.pathname == url
        callback()
