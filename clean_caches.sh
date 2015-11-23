#!/bin/bash
find app/tmp/cache -type f | xargs -I {} rm {}
sudo /etc/init.d/memcached restart

