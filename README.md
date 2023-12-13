# Currency Exchange Rate Project
This project provides a currency exchange rate API, allowing users to fetch and update currency exchange rates. Below are the instructions for setting up the project, using the console command, accessing the API endpoint, and running PHPUnit tests.


## Install Dependencies

Navigate to the project directory and install the PHP dependencies using Composer:


```bash
composer install
```

## Running the Console Command
To update the currency exchange rates, run the following console command:

```bash
php bin/console app:currency:rates EUR USD GBP JPY
```

This command updates the exchange rates for the specified base currency (EUR) against the given target currencies (USD, GBP, JPY).

## Accessing the API Endpoint
To fetch the exchange rates via the API, make a GET request to the following endpoint:

```bash
/api/exchange-rates?base_currency=EUR&target_currencies=USD,GBP,JPY
```

This endpoint returns the exchange rates for the base currency (EUR) against the target currencies (USD, GBP, JPY).

## Running PHPUnit Tests
To run the PHPUnit tests, use the following command in the project root directory:

```bash
./vendor/bin/phpunit
```

This will execute all the PHPUnit tests defined in the project.