# TRT-conseil-app

Snowtricks
It's a Symfony 6 project. a recruitment site.

Getting Started
These instructions will get you a copy of the project up and running on your local machine for development and testing purposes.

Prerequisites
What things you need to install the software and how to install them

PHP 8.1
MySQL 5.7
Installing
First :

Git clone https://github.com/Blacklight059/TRT-conseil-app.git
Update ".env" with your parameters


Install Dependencies :

composer install


Install DB :

php bin/console doctrine:schema:update --force


Install fixtures :

php bin/console doctrine:fixtures:load


To test user features, an user will be created with : 

email : candidate@candidate.fr password : candidate

email : recruiter@recruiter.fr password : recruiter

email : consultant@consultant.fr password : consultant



Terms
Privacy
Security
Status
