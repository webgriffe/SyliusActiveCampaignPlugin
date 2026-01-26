---
title: Opening Sylius in the plugin
layout: page
nav_order: 1
parent: Contributing
---

# Opening Sylius in the plugin

After following the setup steps in the main contributing guide, you can access the Sylius test application running the plugin at:

http://localhost:8080/

## Running the local server

To start the local server and install SSL certificates (only once needed):

```bash
symfony server:ca:install
symfony server:start -d
```

The test application will be available at http://localhost:8080/.

If you need to customize services, create a `compose.override.yml` and start Docker:

```bash
docker-compose up -d
```
