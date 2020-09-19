#!/bin/bash

DOCS_VERSIONS=(
  master
  8.x
  7.x
  6.x
)

for v in "${DOCS_VERSIONS[@]}"; do
    if [ -d "resources/lumen-docs/$v" ]; then
        echo "Pulling latest documentation updates for $v..."
        (cd resources/lumen-docs/$v && git pull)
    else
        echo "Cloning $v..."
        git clone --single-branch --branch "$v" git@github.com:laravel/lumen-docs.git "resources/lumen-docs/$v"
    fi;
done
