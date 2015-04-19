module Whathood

    class Grunt

        def self.is_installed(src_dir,module_name)
            return File.exists?("#{src_dir}/node_modules/#{module_name}")
        end

        # returns true if the method installed
        def self.install_if_missing(src_dir,module_name)
            if !self.is_installed(src_dir,module_name)
                self.install(module_name)
                return true
            end
            return false
        end

        def self.install(module_name)
            `npm install #{module_name} --save-dev`
        end
    end
end
