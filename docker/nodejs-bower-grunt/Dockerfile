#
# Node.js w/ Bower & Grunt Dockerfile
#
# https://github.com/dockerfile/nodejs-bower-grunt
#

# Pull base image.
FROM node

# Install Bower & Grunt
RUN npm install -g grunt-cli

# Define working directory.
WORKDIR /data

# Define default command.
CMD ["grunt"]
