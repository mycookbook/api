# cookbook - api

API backend for cookbook. This forms the core of cookbook.

## Developers Guide

- Backend technology: Lumen 5.4

### Installation

```
mkdir cookbook && cd cookbook
git clone https://github.com/mycookbook/api.git
cd cookbook && cd api
composer install
```

Create a .env file and copy the contents of .env.example into that 
file, configure your dev environment accordingly then run the 
migrations followed by the db seeder:

```
php artisan migrate
php artisan db:seed --class=UsersTableSeeder
```
    
Serve the app:

    php -S localhost:8000 -t public

Running tests

```
./vendor/bin/phpunit tests
```

### Contributing

You can contribute in a number of ways.

- pick a story from the trello board (you need to be invited to the board)
- raise an issue using the issues tab
- find a bug you will like to fix or a piece of code you can make better? raise a PR

### Raising a pull request

- write tests against your code
- raise a pull request against develop branch
- fix any conflicts that may arise
- ask for code review, you need atleast 2 reviews for your PR to be accepted

### Contributors
- [Elisha Chijioke](https://github.com/andela-celisha-wigwe)
- [George James](https://github.com/sslgeorge)
- [Suraj Akande](#)
