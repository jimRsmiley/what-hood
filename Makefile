SRC_DIR="/var/www/whathood"


all: grunt

grunt:
	$(SRC_DIR)/bin/node_install.rb
	grunt coffee

clean-grunt:
	rm -rf /var/www/whathood/node_modules
	rm -rf /var/www/whathood/app/public/js/whathood/whathood-compiled.js

clean: clean-grunt
	rm -rf /opt/whathood/*
	rm -rf /var/www/whathood/node_modules

test: phpunit

phpunit:
	phpunit -c app/module/Whathood/test

composer-install:
	@mkdir --parent /var/tmp/composer
	@sudo docker run -ti \
		-v `pwd`/app:/srv \
		-v /var/tmp/composer:/root/.composer \
		quay.io/whathood/composer install
