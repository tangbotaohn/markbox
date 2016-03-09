#!/bin/bash

composer update
composer dump-autoload
chmod -R 777 ./storages
