# THIS RUNS INSIDE THE NGINX CONTAINER


# watch for coffeescript changes and rebuild if the code changes
coffee: grunt watch

# doing `sudo stop wh-foreman` will not stop this process, you'll need to do a `ps aux | grep process_queue | grep -v grep | awk '{print $2}' | sudo xargs kill -SIGINT` also
message_queue: rerun --name "Queue Worker" --background --pattern '*.php' -d app/module/Whathood bin/process_queue
