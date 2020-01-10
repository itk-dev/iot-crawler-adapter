# iot-crawler-adapter

## API

https://gitlab.iotcrawler.net/core/iotcrawler_core/snippets/7#note_279

## Users

```sh
bin/console app:user:create --help
```

```sh
bin/console app:user:list
```

## Tests

```sh
APP_ENV=test bin/console doctrine:database:create
```

```sh
APP_ENV=test bin/console doctrine:migrations:migrate --no-interaction
APP_ENV=test bin/console doctrine:fixtures:load --no-interaction --group=test
bin/phpunit tests/Controller/LoriotControllerTest.php
```
