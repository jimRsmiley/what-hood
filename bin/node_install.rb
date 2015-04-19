#!/usr/bin/env ruby

require_relative '../libs/ruby/Whathood/Grunt.rb'

APP_DIR = '/var/www/whathood'

packages = [
    'grunt',
    'grunt-contrib-watch',
    'grunt-contrib-coffee',
    'grunt-contrib-clean'
]

packages.each do |package|
    puts "installing npm package #{package}"
    result = Whathood::Grunt.install_if_missing(APP_DIR,package)
    if result
        puts "installed npm package #{package}"
    else
        puts "npm package #{package} already installed"
    end
end
