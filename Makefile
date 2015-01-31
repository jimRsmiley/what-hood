all: grunt

grunt:
	npm install grunt --save-dev
	npm install grunt-contrib-watch --save-dev
	npm install grunt-contrib-coffee --save-dev

clean:
	rm -rf /opt/whathood
	rm -rf /var/www/whathood/node_modules
