# Installation

[Return to Summary main page](README.md)

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
