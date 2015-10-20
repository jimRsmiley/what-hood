# THIS RUNS INSIDE THE NGINX CONTAINER


# watch for coffeescript changes and rebuild if the code changes
grunt_watch: bin/grunt watch

# current directory implicitly watched
rerun_message_queue: rerun --name "Dev Dir Change Queue Worker Monitor" --background -d app --pattern '"*.{php,rb,yaml}"' --ignore 'test/' --ignore 'view/' --ignore 'vendor/' --ignore 'data/' -- sudo stop wh-worker; sudo start wh-worker
