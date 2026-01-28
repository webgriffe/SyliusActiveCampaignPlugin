---
title: Contributing
layout: page
nav_order: 4
has_children: true
---

# Contributing

To contribute you need to:

1. Clone this repository into your development environment and go to the plugin's root directory.
2. Run:
   ```bash
   composer install
   ```
3. Copy `tests/TestApplication/.env` to `tests/TestApplication/.env.local` and set configuration specific for your development environment.
4. Link node_modules:
   ```bash
   ln -s vendor/sylius/test-application/node_modules node_modules
   ```
5. Run docker (create a `compose.override.yml` if you need to customize services):
   ```bash
   docker-compose up -d
   ```
6. Initialize the test application:
   ```bash
   composer test-app-init
   ```
7. Run your local server:
   ```bash
   symfony server:ca:install
   symfony server:start -d
   ```
8. Now at http://localhost:8080/ you have a full Sylius testing application which runs the plugin.

## Static checks

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

## License

This library is under the MIT license. See the complete license in the LICENSE file.

## Credits

Developed by [Webgriffe®](http://www.webgriffe.com/).
