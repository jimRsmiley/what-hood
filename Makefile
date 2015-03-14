all: grunt

grunt:
	npm update grunt --save-dev
	npm update grunt-contrib-watch --save-dev
	npm update grunt-contrib-coffee --save-dev
	npm update grunt-contrib-clean --save-dev
	grunt coffee

clean-grunt:
	rm -rf /var/www/whathood/node_modules
	rm -rf /var/www/whathood/app/public/js/whathood/whathood-compiled.js

clean: clean-grunt
	rm -rf /opt/whathood/*
	rm -rf /var/www/whathood/node_modules
