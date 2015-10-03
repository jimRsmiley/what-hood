module Whathood

  module Config
    require_relative 'Util.rb'
    USER_HOME = ENV['HOME']
    GIT_DIR     = "#{USER_HOME}/src/whathood"
    DOCKER_GUEST_SRC_DIR = "/var/www/whathood"
    APPLICATION_ENV = Util.config_val "application_env"
  end

end
