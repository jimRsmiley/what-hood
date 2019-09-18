FROM quay.io/whathood/nodejs-bower-grunt

# code mounts in /data
# output should mount in /output

RUN npm install -g codo

RUN mkdir /output

WORKDIR /data

ENTRYPOINT ["codo"]
