# Events-API
A sample project demonstrating various components of web API application.

## Installation
- Clone the repository. `git clone https://github.com/oluudeh/Events-API.git`
- Rename `example.env` to `.env`, then supply environment variable values.
- Run `docker-compose up` or `docker compose up` depending on what version of Docker Compose you are running.
- Launch docker container CLI `docker exec -it events_app_1  /bin/sh`.
- Run migration to create database tables: `php ./src/console/migrate.php`.
- Seed database tables with data: `php ./src/console/seed.php`.

## Documentation
The documentation for this project can be found in `spec/api-spec.yaml`. This file can be imported into Postman and used with Swagger.

## Tests
Tests can be found inside `tests` folder. To run tests use `./vendor/bin/phpunit`.
