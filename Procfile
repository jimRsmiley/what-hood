# THIS RUNS INSIDE THE NGINX CONTAINER
coffee: grunt watch
message_queue: rerun --name "Queue Worker" --background --pattern '*.php' -d app/module/Whathood bin/process_queue
