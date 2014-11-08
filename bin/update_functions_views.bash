#!/bin/bash

find ./app/scripts/sql -name whathood.function.*.sql -exec cat {} \; | psql jim_whathood
find ./app/scripts/sql -name whathood.view.*.sql -exec cat {} \; | psql jim_whathood
