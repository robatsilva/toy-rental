#!/bin/bash

echo Uploading Application container 
docker-compose up -d

echo Copying the configuration example file
docker exec -it rental-toy-app-p cp env.example .env

echo Install dependencies
docker exec -it rental-toy-app-p composer install

echo Generate key
docker exec -it rental-toy-app-p php artisan key:generate

echo Make migrations
docker exec -it rental-toy-app-p php artisan migrate

echo Make seeds
docker exec -it rental-toy-app-p php artisan db:seed

echo Information of new containers
docker ps -a 


echo instalar passaport
docker exec -it rental-toy-app-p composer require laravel/passport

echo make migrations
docker exec -it  rental-toy-app-p php artisan migrate