GRUNT_BIN="./bin/grunt"

all: javascript

javascript:
	$(GRUNT_BIN) javascript

coffee:
	$(GRUNT_BIN) coffee

grunt:
	$(GRUNT_BIN) coffee

clean: clean-build clean-node-modules

clean-public:
	rm -rf app/public/js/*
	rm -rf app/public/*.css

clean-node-modules:
	rm -rf ./node_modules

clean-build:
	rm -rf ./build

test: phpunit

phpunit:
	phpunit -c app/module/Whathood/test

composer-install:
	@mkdir --parent /var/tmp/composer
	@sudo docker run \
		-v `pwd`/app:/srv \
		-v /var/tmp/composer:/root/.composer \
		whathood/composer install

composer-dumpautoload:
	@mkdir --parent /var/tmp/composer
	@sudo docker run \
		-v `pwd`/app:/srv \
		-v /var/tmp/composer:/root/.composer \
		whathood/composer dumpautoload -o
