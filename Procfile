# THIS RUNS INSIDE THE NGINX CONTAINER


# watch for coffeescript changes and rebuild if the code changes
grunt_watch: grunt watch

# current directory implicitly watched
rerun_message_queue: rerun --name "Dev Dir Change Queue Worker Monitor" --background --pattern '"*.{php,rb,yaml}"' --ignore 'vendor/' --ignore 'data/' -- sudo stop wh-worker; sudo start wh-worker
