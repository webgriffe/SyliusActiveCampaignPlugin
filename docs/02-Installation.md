# Installation

[Return to Summary main page](README.md)

1. Run
    ```shell
    composer require webgriffe/sylius-active-campaign-plugin
    ```

2. Add `Webgriffe\SyliusActiveCampaignPlugin\WebgriffeSyliusActiveCampaignPlugin::class => ['all' => true]` to your `config/bundles.php`.

3. Configure your ActiveCampaign API connection parameters by creating the `config/packages/webgriffe_sylius_active_campaign_plugin.yaml` file with the following content:
    ```yaml
    imports:
        - { resource: "@WebgriffeSyliusActiveCampaignPlugin/Resources/config/app/config.yaml" }

    webgriffe_sylius_active_campaign:
        api_client:
            base_url: 'https://www.activecampaign.com/'
            key: 'SECRET'
    ```
   Pay attention that among these parameters there are some sensitive configuration that you probably don't want to
   commit in your VCS. There are different solutions to this problem, like env configurations and secrets. Refer
   to [Symfony best practices doc](https://symfony.com/doc/current/best_practices.html#configuration) for more info.

4. Import the routes needed for updating the list status of contact (you can omit this if you don't need to update the list status, or you don't use the list subscription feature). Add the following to your `config/routes.yaml` file:
   ```yaml
   webgriffe_sylius_active_campaign_shop:
       resource: "@WebgriffeSyliusActiveCampaignPlugin/Resources/config/app_routing.yml"
    ```
   Note that these routes shouldn't be inside your "shop routes", the locale parameter is not needed.

5. Your `Customer` entity must implement
   the `Webgriffe\SyliusActiveCampaignPlugin\Model\CustomerActiveCampaignAwareInterface`. You can use
   the `Webgriffe\SyliusActiveCampaignPlugin\Model\ActiveCampaignAwareTrait` and
   the `Webgriffe\SyliusActiveCampaignPlugin\Model\CustomerActiveCampaignAwareTrait` as implementations for the
   interface. The Customer entity should look like this:
    ```php
    <?php
	
    namespace App\Entity\Customer;
	
    use Doctrine\ORM\Mapping as ORM;
    use Sylius\Component\Core\Model\Customer as BaseCustomer;
    use Webgriffe\SyliusActiveCampaignPlugin\Model\ActiveCampaignAwareTrait;
    use Webgriffe\SyliusActiveCampaignPlugin\Model\CustomerActiveCampaignAwareInterface;
    use Webgriffe\SyliusActiveCampaignPlugin\Model\CustomerActiveCampaignAwareTrait;
	
    /**
     * @ORM\Entity
     * @ORM\Table(name="sylius_customer")
     */
    class Customer extends BaseCustomer implements CustomerActiveCampaignAwareInterface
    {
        use ActiveCampaignAwareTrait;
        use CustomerActiveCampaignAwareTrait;
    }
    ```

6. Your `Channel` entity must implement
   the `Webgriffe\SyliusActiveCampaignPlugin\Model\ChannelActiveCampaignAwareInterface`. You can use
   the `Webgriffe\SyliusActiveCampaignPlugin\Model\ActiveCampaignAwareTrait` and
   the `Webgriffe\SyliusActiveCampaignPlugin\Model\ChannelActiveCampaignAwareTrait` as implementation for the
   interface. The Channel entity should look like this:
   ```php
   <?php
   
   namespace App\Entity\Channel;

   use Doctrine\ORM\Mapping as ORM;
   use Sylius\Component\Core\Model\Channel as BaseChannel;
   use Webgriffe\SyliusActiveCampaignPlugin\Model\ActiveCampaignAwareTrait;
   use Webgriffe\SyliusActiveCampaignPlugin\Model\ChannelActiveCampaignAwareInterface;
   use Webgriffe\SyliusActiveCampaignPlugin\Model\ChannelActiveCampaignAwareTrait;

   /**
    * @ORM\Entity
    * @ORM\Table(name="sylius_channel")
    */
   class Channel extends BaseChannel implements ChannelActiveCampaignAwareInterface
   {
       use ActiveCampaignAwareTrait;
       use ChannelActiveCampaignAwareTrait;
   }
   ```

7. Your `Order` entity must implement the `Webgriffe\SyliusActiveCampaignPlugin\Model\ActiveCampaignAwareInterface`. You
   can use the `Webgriffe\SyliusActiveCampaignPlugin\Model\ActiveCampaignAwareTrait` as implementation for the
   interface. The Order entity should look like this:
   ```php
   <?php
   
   namespace App\Entity\Order;

   use Doctrine\ORM\Mapping as ORM;
   use Sylius\Component\Core\Model\Order as BaseOrder;
   use Webgriffe\SyliusActiveCampaignPlugin\Model\ActiveCampaignAwareInterface;
   use Webgriffe\SyliusActiveCampaignPlugin\Model\ActiveCampaignAwareTrait;

   /**
    * @ORM\Entity
    * @ORM\Table(name="sylius_order")
    */
   class Order extends BaseOrder implements ActiveCampaignAwareInterface
   {
       use ActiveCampaignAwareTrait;
   }
   ```

8. The `SyliusActiveCampaignPlugin` needs to store the `ActiveCampaign Ecommerce Customer's id` on a Channel-Customer
   association that should implement the `Webgriffe\SyliusActiveCampaignPlugin\Model\ChannelCustomerInterface`. If you
   don't already have an association like this in you project you could use the plugin's ChannelCustomer resource by
   adding a `ChannelCustomer` entity and make it extending
   the `Webgriffe\SyliusActiveCampaignPlugin\Model\ChannelCustomer` class. You can have a look at the following example:
   ```php
   <?php
   
   namespace App\Entity\Customer;
   
   use Doctrine\ORM\Mapping as ORM;
   use Webgriffe\SyliusActiveCampaignPlugin\Model\ChannelCustomer as BaseChannelCustomer;
   
   /**
    * @ORM\Entity
    * @ORM\Table(name="webgriffe_sylius_active_campaign_channel_customer")
    */
   class ChannelCustomer extends BaseChannelCustomer
   {
   }
   ```
   If you have added the ChannelCustomer entity be sure to mark it as a Sylius Resource by adding the following lines in
   the `webgriffe_sylius_active_campaign_plugin.yaml` file:

    ```yaml
    webgriffe_sylius_active_campaign:
        ...
        resources:
            channel_customer:
                classes:
                    model: App\Entity\Customer\ChannelCustomer
    ```

9. Your `CustomerRepository` class must implement
    the `Webgriffe\SyliusActiveCampaignPlugin\Repository\ActiveCampaignCustomerRepositoryInterface`. You can use
    the `Webgriffe\SyliusActiveCampaignPlugin\Doctrine\ORM\ActiveCampaignCustomerRepositoryTrait` as implementation for
    the interface if you use Doctrine ORM and extends the Sylius Customer Repository. Remember [to add the repository in your sylius_resource configuration](https://docs.sylius.com/en/latest/customization/repository.html).

10. Your `ChannelRepository` class must implement
     the `Webgriffe\SyliusActiveCampaignPlugin\Repository\ActiveCampaignResourceRepositoryInterface`. You can use
     the `Webgriffe\SyliusActiveCampaignPlugin\Doctrine\ORM\ActiveCampaignChannelRepositoryTrait` as implementation for
     the interface if you use Doctrine ORM and extends the Sylius Channel Repository. Remember [to add the repository in your sylius_resource configuration](https://docs.sylius.com/en/latest/customization/repository.html).

11. Your `OrderRepository` class must implement
    the `Webgriffe\SyliusActiveCampaignPlugin\Repository\ActiveCampaignOrderRepositoryInterface`. You can use
    the `Webgriffe\SyliusActiveCampaignPlugin\Doctrine\ORM\ActiveCampaignOrderRepositoryTrait` as implementation for the
    interface if you use Doctrine ORM and extends the Sylius Order Repository. Remember [to add the repository in your sylius_resource configuration](https://docs.sylius.com/en/latest/customization/repository.html).

12. If you use `Doctrine` and you have used the traits of the plugin you should run a diff of your Doctrine's schema and
   then run the migration generated:
   ```shell
   bin/console doctrine:migrations:diff
   bin/console doctrine:migrations:migrate
   ```
