<p align="center">
    <a href="https://sylius.com" target="_blank">
        <img src="https://demo.sylius.com/assets/shop/img/logo.png" />
    </a>
</p>

<h1 align="center">ActiveCampaign Plugin</h1>

<p align="center">Sylius plugin to integrate the marketing #1 automation platform</p>

## Quickstart Installation

1. Run
    ```shell
    composer require webgriffe/sylius-active-campaign-plugin
    ```

2. Configure your ActiveCampaign API connection parameters. Edit the `config/packages/webgriffe_sylius_active_campaign_plugin.yaml` file by adding the following content:
    ```yaml
    webgriffe_sylius_active_campaign:
        api_client:
            base_url: 'https://www.activecampaign.com/'
            key: 'SECRET'
    ```
    Pay attention that among these parameters there are some sensitive configuration that you probably don't want to commit in your VCS. There are different solutions to this problem, like env configurations and secrets. Refer to [Symfony best practices doc](https://symfony.com/doc/current/best_practices.html#configuration) for more info.

3. Your `Customer` entity must implement the `Webgriffe\SyliusActiveCampaignPlugin\Model\ActiveCampaignAwareInterface`. You can use the `Webgriffe\SyliusActiveCampaignPlugin\Model\ActiveCampaignAwareTrait` as implementation for the interface.

4. If you have used the `ActiveCampaignAwareTrait` you should run a diff of your Doctrine's migrations and then run it:
   ```shell
   bin/console doctrine:migrations:diff
   bin/console doctrine:migrations:migrate
   ```

5. Your `CustomerRepository` class must implement the `Webgriffe\SyliusActiveCampaignPlugin\Repository\ActiveCampaignAwareRepositoryInterface`. You can use the `Webgriffe\SyliusActiveCampaignPlugin\Doctrine\ORM\ActiveCampaignCustomerRepositoryTrait` as implementation for the interface if you use Doctrine ORM and extends the Sylius Customer Repository.

## Usage

WIP

## Documentation

WIP

## Contributing

In order to contribute to this plugin you have to clone this repository, create a branch for your feature or bugfix, do your changes and then make sure al tests are passing.

    ```bash
    $ (cd tests/Application && yarn install)
    $ (cd tests/Application && yarn build)
    $ (cd tests/Application && APP_ENV=test bin/console assets:install public)
    
    $ (cd tests/Application && APP_ENV=test bin/console doctrine:database:create)
    $ (cd tests/Application && APP_ENV=test bin/console doctrine:schema:create)
    ```

To be able to setup a plugin's database, remember to configure you database credentials in `tests/Application/.env` and `tests/Application/.env.test`.

### Running plugin tests

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
      APP_ENV=test symfony server:start --port=8080 --dir=tests/Application/public --daemon
      ```
    
    4. Run Behat:
    
      ```bash
      vendor/bin/behat --strict --tags="@javascript"
      ```
    
  - Static Analysis
  
    - Psalm
    
      ```bash
      vendor/bin/psalm
      ```
      
    - PHPStan
    
      ```bash
      vendor/bin/phpstan analyse
      ```

  - Coding Standard
  
    ```bash
    vendor/bin/ecs check
    ```

### Opening Sylius with your plugin

- Using `test` environment:

    ```bash
    (cd tests/Application && APP_ENV=test bin/console sylius:fixtures:load)
    (cd tests/Application && APP_ENV=test bin/console server:run -d public)
    ```
    
- Using `dev` environment:

    ```bash
    (cd tests/Application && APP_ENV=dev bin/console sylius:fixtures:load)
    (cd tests/Application && APP_ENV=dev bin/console server:run -d public)
    ```
