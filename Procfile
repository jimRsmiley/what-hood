# THIS RUNS INSIDE THE NGINX CONTAINER


# watch for coffeescript changes and rebuild if the code changes
coffee: grunt watch

message_queue: rerun --name "Queue Worker" --background --pattern '*.php' -d app/module/Whathood -- sudo stop wh-worker; sudo start wh-worker
