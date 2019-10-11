# How to install

run the following commands

- `composer install`
- `touch database/database.sqlite`
-  rename the file `.env.example` to `.env`
- `php artisan migrate`


# To scrap the data

- deploy the app
- `/init` route will scrap the data and save it in db
- to get the data you can ping the route `/list` of the app
- or you can directly read from the sqlite db