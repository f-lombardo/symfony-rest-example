# symfony-rest-example

A REST API with Symfony.

This example exposes a CRUD REST API for an entity called Book.

## Running the application

To run the project follow the following steps, that assume you have `docker` and `docker compose` installed on your
machine.

1. Clone this repository
2. Start the required containers using `docker compose`:

```shell
cd docker
docker compose up -d
```

3. Install the required dependencies (still from the docker directory):

```shell
docker compose exec -T php sh -c 'composer install'
```

4. Connect to [http://localhost:8099/books](http://localhost:8099/books)

Step number 2 will start three containers:

- a php container with fpm process manager
- a ngnix http server that answers on port 8099 (you can change this editing the [.env](./docker/.env)) file)
- a PostgreSQL server that answers on port 5499 (you can change this port as well).

## Running tests and checks

To run unit and integration tests, after having installed dependencies (see above) follow these steps.

1. Connect to the php container:

```shell
cd docker
docker compose exec php bash
```

2. From the container run:

```shell
composer quality
composer unit-tests
composer functional-tests
```

The `quality` script runs the code [linter](https://laravel.com/docs/11.x/pint) and [`phpstan`](https://phpstan.org)
checks.
The other two scripts runs unit tests and functional (BDD) tests. Please note that at this moment we still do not have
unit tests,
since an almost complete code coverage has been reached just with [Behat](https://docs.behat.org/en/latest/) tests.
To be honest, I strongly believe that BDD (or
better [ATDD](https://en.wikipedia.org/wiki/Acceptance_test-driven_development))
tests are more robust and efficient than unit tests,
as I discussed in some presentation at Agile Day 2023 and at Crafted Software, but more on this for another time ;-)

