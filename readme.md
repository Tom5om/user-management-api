## About this project

This project has been built using Laravel and a boilerplate that I have helped develop with my friend. It provides generic REST functionality out of the box.
It utilises laradock which will make it easy to set the project up locally.

Prerequisites
* On Linux, probably mac as well (untested on mac)
* Docker
* Docker-compose
* node
* npm


## Project Setup

1. clone the 2 repos {INSERT REPOS HERE]
2. Frontend

```bash
npm install
npm run serve
```

3. Backend
```bash
cd laradock
git submodule init
git submodule update
cp env-example .env
docker-compose up -d
docker-compose exec --user=laradock workspace bash
composer install
composer run post-root-package-install
cp .env.example .env
php artisan key:generate
php artisan jwt:secret
php artisan ide-helper:generate
artisan migrate
php artisan db:seed
```

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


## Boilerplate Documentation
All functionality and use cases provided by this boilerplate are documented n the [Project Wiki](https://github.com/specialtactics/l5-api-boilerplate/wiki).

## Check out the documentation of supporting projects

Every great project stands on the shoulders of giants. Check out the documentation of these key projects to learn more.

 - [Laravel 5 API boilerplate](https://github.com/specialtactics/l5-api-boilerplate)
 - [Laravel](https://laravel.com/docs/)
 - [Dingo API](https://github.com/dingo/api/wiki)
 - [Tymon JWT Auth](https://github.com/tymondesigns/jwt-auth)
 - [League Fractal](https://fractal.thephpleague.com/)
 - [Laravel UUID](https://github.com/webpatser/laravel-uuid/tree/2.1.1)

