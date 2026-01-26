---
title: Running tests
layout: page
nav_order: 2
parent: Contributing
---

# Running tests

After your changes you must ensure that the tests are still passing.

## Setup test database

```bash
APP_ENV=test vendor/bin/console doctrine:database:create
APP_ENV=test vendor/bin/console doctrine:migrations:migrate -n
# Optionally load data fixtures
APP_ENV=test vendor/bin/console sylius:fixtures:load -n
```

## Build assets

```bash
(cd vendor/sylius/test-application && yarn install)
(cd vendor/sylius/test-application && yarn build)
vendor/bin/console assets:install
```

## Test suite

- PHPUnit
  ```bash
  vendor/bin/phpunit
  ```
- PHPSpec
  ```bash
  vendor/bin/phpspec run
  ```
- Behat (non-JS scenarios)
  ```bash
  vendor/bin/behat --strict --tags="~@javascript"
  ```
- Behat (JS scenarios)
  1. [Install Symfony CLI command](https://symfony.com/download).
  2. Start Headless Chrome:
    ```bash
    google-chrome-stable --enable-automation --disable-background-networking --no-default-browser-check --no-first-run --disable-popup-blocking --disable-default-apps --allow-insecure-localhost --disable-translate --disable-extensions --no-sandbox --enable-features=Metal --headless --remote-debugging-port=9222 --window-size=2880,1800 --proxy-server='direct://' --proxy-bypass-list='*' http://127.0.0.1
    ```
  3. Install SSL certificates (only once needed) and run test application's webserver on `127.0.0.1:8080`:
    ```bash
    symfony server:ca:install
    APP_ENV=test symfony server:start --port=8080 --dir=tests/TestApplication/public --daemon
    ```
  4. Run Behat:
    ```bash
    vendor/bin/behat --strict --tags="@javascript"
    ```

## Static Analysis

- Coding Standard
  ```bash
  vendor/bin/ecs check --fix
  ```
- Psalm
  ```bash
  vendor/bin/psalm
  ```
- PHPStan
  ```bash
  vendor/bin/phpstan analyse
  ```
