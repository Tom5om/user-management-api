## About this project

This project has been built using Laravel and a boilerplate that I have developed with a friend. It provides generic REST functionality out of the box.
This project utilises laradock which will make it easy to set the project up locally.

Prerequisites
* On Linux, probably mac as well (untested on mac)
* Docker
* Docker-compose
* node
* npm


## Project Setup

1. Frontend

```bash
git clone https://github.com/Tom5om/user-management-frontend.git
cd user-management-frontend
npm install
npm run serve
```

2. Backend
```bash
git clone https://github.com/Tom5om/user-management-api.git
cd user-management-api
cd laradock
git submodule init
git submodule update
cp env-example .env
docker-compose up -d
docker-compose exec --user=laradock workspace bash
composer install
composer run post-root-package-install
php artisan key:generate
php artisan jwt:secret
php artisan ide-helper:generate
artisan migrate
php artisan db:seed
```
3. go to http://localhost:8080 & click register
4. Go to maildev: http://localhost:1080 click the link to verify email
5. Login
6. Click on your name on the top left

## urls
* Frontend: http://localhost:8080
* Backend: http://localhost
* Telescope: http://localhost/telescope
* Maildev: http://localhost:1080
* PHPmyadmin: http://localhost:8079 -- host: mariadb --username: default --password: secret

## Application example
1. Go to register -> register a user
2. See maildev to receive the email
3. Click on link in email to verify user
4. Login using credentials used in registration
5. For the profile manage page, click on your name in the top left
5. Update profile or upload an image

## Check out the documentation of supporting projects

Every great project stands on the shoulders of giants. Check out the documentation of these key projects to learn more.

 - [Laravel 5 API boilerplate](https://github.com/specialtactics/l5-api-boilerplate)
 - [Laravel](https://laravel.com/docs/)
 - [Dingo API](https://github.com/dingo/api/wiki)
 - [Tymon JWT Auth](https://github.com/tymondesigns/jwt-auth)
 - [League Fractal](https://fractal.thephpleague.com/)
 - [Laravel UUID](https://github.com/webpatser/laravel-uuid/tree/2.1.1)

