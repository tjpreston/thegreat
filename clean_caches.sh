#!/bin/bash
find app/tmp/cache -type f | xargs -I {} rm {}
sudo service memcached restart

