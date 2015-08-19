#!/bin/sh

sudo docker exec -it wh-nginx /bin/sh -c 'cd /var/www/whathood/app/module/Whathood; ../../vendor/bin/classmap_generator.php'
