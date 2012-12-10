#!/bin/bash

while true; do
    while inotifywait -r -e modify -e create /home/www/museotouch/uploads/objets/; do
        python newsync.py
    done
done
