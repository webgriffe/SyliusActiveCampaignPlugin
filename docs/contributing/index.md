---
title: Contributing
layout: page
nav_order: 4
has_children: true
---

# Contributing

In order to contribute to this plugin you have to clone this repository, create a branch for your feature or bugfix, do
your changes and then make sure al tests are passing.

```bash
(cd tests/Application && yarn install)
(cd tests/Application && yarn build)
(cd tests/Application && APP_ENV=test bin/console assets:install public)

(cd tests/Application && APP_ENV=test bin/console doctrine:database:create)
(cd tests/Application && APP_ENV=test bin/console doctrine:schema:create)
```

To be able to set up a plugin's database, remember to configure you database credentials in `tests/Application/.env`
and `tests/Application/.env.test`.
