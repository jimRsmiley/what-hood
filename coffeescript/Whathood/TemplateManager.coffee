window = exports ? this
Whathood = window.Whathood

class Whathood.TemplateManager

  constructor: () ->
    @templates = new Array
    @contexts  = new Array

  add_template: (name, template) ->
    @templates[name] = template

  load_template: (name, path, callback) ->
    path = "/js/whathood/templates/#{path}.handlebars"

    template = @templates[name]
    throw new Error("Template key #{name} already exists") if template?

    if !template?
      $.ajax
        url: path
        success: (template_source) =>
          @add_template name, Handlebars.compile(template_source)
          callback() if callback?
    else
      callback() if callback?

  transform: (name, context) ->
    # load the last context if the passed one is blank
    context = @contexts[name] if !context?
    template = @templates[name]

    throw new Error "template w name #{name} does not exist" unless template
    @contexts[name] = context
    template context

