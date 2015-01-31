all: grunt

grunt:
	npm install --silent grunt --save-dev
	npm install --silent grunt-contrib-watch --save-dev 
	npm install --silent grunt-contrib-coffee --save-dev 

clean:
	rm -rf /opt/whathood
