all: grunt

grunt:
	npm install grunt --save-dev
	npm install grunt-contrib-watch --save-dev
	npm install grunt-contrib-coffee --save-dev
	npm install grunt-contrib-clean --save-dev

clean-grunt:
	rm -rf /var/www/whathood/node_modules

clean: clean-grunt
	rm -rf /opt/whathood
	rm -rf /var/www/whathood/node_modules
