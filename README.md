## The PHP app to find duration between timestamps

The PHP app to find the breakdown of the duration between the two timestamps using the time expressions.

- The application exposes an endpoint that accepts two timestamps and a list of time expressions
- And returns a breakdown of the duration between the two timestamps using the given time expressions
- The application saves all the breakdowns executed in the sqlite db
- Another endpoint exposed to search these stored breakdowns by the input timestamps

You can see a hosted version of the app on <a href="https://guarded-cliffs-92027.herokuapp.com/" target="_blank">
Heroku</a>.

## Requirements

* PHP
* Composer

## Common setup

Clone the repo and install the dependencies.

``` bash
git clone https://github.com/bipinktr/time_breakdown.git
cd time_breakdown/
```

``` bash
mv .env.example .env
touch database/database.sqlite
composer install
php artisan migrate --seed
```

## Steps for testing

``` bash
vendor/bin/phpunit
```

## Steps for access

To start the local server, run the following:

``` bash
php -S localhost:8000 -t public
```

## Steps for read and write access

Note: The rest api is authenticated with an api_token either pass as header or input param

``` bash
header 'api_token: feb5b42b'
```

Step 1: Generate and save breakdown of the duration.

``` bash
curl --location --request POST 'http://localhost:8000/api/breakdown' \
--header 'api_token: feb5b42b' \
--header 'Content-Type: application/json' \
--data-raw '{
    "start_date": "2020-01-01T00:00:00",
    "end_date": "2020-03-01T12:30:00",
    "time_expressions": ["2m", "m", "d", "2h"]
}'
```

Step 2: Filter stored breakdowns.

``` bash
curl --location --request GET 'http://localhost:8000/api/breakdown?start_date=2020-01-01T00:00:00&end_date=2020-03-01T12:30:00' \
--header 'api_token: feb5b42b'
```

## Deploy to Heroku

You can also deploy this app to Heroku:

[![Deploy](https://www.herokucdn.com/deploy/button.svg)](https://heroku.com/deploy)
