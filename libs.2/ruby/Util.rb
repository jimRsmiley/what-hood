require 'yaml'

class Util

    # return a string representing the application environment
    def self.application_env
        return 'production'
    end

    def self.run_cmds(cmds)
        cmds.each { |cmd|
            puts cmd
            exitstatus = self.run_cmd(cmd)

            unless exitstatus
                abort "command failed with status(#{status}): #{cmd}"
            end
        }
    end

    def self.run_cmd (cmd)
        result = `#{cmd}`

        unless $?.exitstatus
            abort "command failed: #{cmd}"
        end

        return $?.exitstatus
    end

    def self.local_config
        if File.exists? 'config.local.yaml'
          return YAML.load_file 'config.local.yaml'
        end
        return
    end

    def self.config_val(key)
        config = YAML.load_file 'config.yaml'
        config_local = self.local_config()
        if config_local and config_local.has_key? key
          return config_local[key]
        else
          return config[key]
        end
    end

end
