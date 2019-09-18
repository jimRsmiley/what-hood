#!/bin/sh

sudo docker rmi quay.io/whathood/nginx-dev || true
sudo docker build -t quay.io/whathood/nginx-dev .
