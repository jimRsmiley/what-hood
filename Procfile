# THIS RUNS INSIDE THE NGINX CONTAINER


# watch for coffeescript changes and rebuild if the code changes
coffee: grunt watch

message_queue: rerun --name "Dev Dir Change Queue Worker Monitor" --background --pattern '"*.{php,rb,yaml}"' -d './' --ignore 'vendor/' --ignore 'data/' -- sudo stop wh-worker; sudo start wh-worker
