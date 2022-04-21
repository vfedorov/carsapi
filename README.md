# Backend Cars API

## Used technologies

PHP Symfony Framework

SQLite

## Installation

Run the following commands in the following order:
```
composer install
composer post-install-migrate
```

## Tests

Run the following command for tests:
```
php bin/phpunit
```

## Endpoints

```
Adding a new car:    POST     /cars
Get car:             GET      /car/<id>
Remove car:          DELETE   /cars/<id>
Get a list of cars:  GET      /cars
```

```
Adding additional colour:   POST    /colours
Editing additional colour:  POST    /colours/<id>/edit
Remove additional colour:   DELETE  /colours/<id>
Get a list of colours:      GET     /colours
```

## Data Models

### Car

 * ID (integer)
 * Make (string)
 * Model (string)
 * Build Date (date)
 * Colour ID (integer)

### Colour

 * ID (integer)
 * Name (string)
 * Editable (boolean)
