# THIS RUNS INSIDE THE NGINX CONTAINER

zend: tail -n0 -F /var/log/whathood/php-zend.log
nginx-error: tail -f /var/log/whathood/nginx-error.log
nginx-access: tail -f /var/log/whathood/nginx-access.log

# watch for coffeescript changes and rebuild if the code changes
grunt_watch: bin/grunt watch

# current directory implicitly watched
rerun_message_queue: rerun --name "Dev Dir Change Queue Worker Monitor" --background -d app --pattern '"*.{php,rb,yaml}"' --ignore 'test/' --ignore 'view/' --ignore 'vendor/' --ignore 'data/' -- sudo stop wh-worker; sudo start wh-worker
