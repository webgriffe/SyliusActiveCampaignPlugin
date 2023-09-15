# UPGRADE FROM `v0.1.0` TO `v0.X`

## UPGRADE FROM `v0.8.0` TO `v0.9.0`

Some services have been removed, please check if you are using them in your application and replace them:

* The `webgriffe.sylius_active_campaign_plugin.serializer` service has been removed, now we use the default `serializer`, please see #78 for more details.
* Now the plugin messages use the `webgriffe_sylius_active_campaign_plugin.command_bus` messenger bus with some middleware, please see #80 for more details.
* The `webgriffe.sylius_active_campaign_plugin.logger` service has been removed. Even the `webgriffe_sylius_active_campaign.logger.channel_name` parameter. Now we use a channel `webgriffe_sylius_active_campaign_plugin` on monolog, please see #79 for more details.

## UPGRADE FROM `v0.6.0` TO `v0.7.0`

The plugin directory structure has been updated to follow the Symfony bundle best practices.
Adjust your `config/packages/webgriffe_sylius_active_campaign_plugin.yaml` file by removing the word `Resources`:
```diff
-   - { resource: "@WebgriffeSyliusActiveCampaignPlugin/Resources/config/app/config.yaml" }
+   - { resource: "@WebgriffeSyliusActiveCampaignPlugin/config/app/config.yaml" }
```

Update your route config import file by removing the word `Resources`:
```diff
webgriffe_sylius_active_campaign_shop:
-   resource: "@WebgriffeSyliusActiveCampaignPlugin/Resources/config/app_routing.yml"
+   resource: "@WebgriffeSyliusActiveCampaignPlugin/config/app_routing.yml"
```

## UPGRADE FROM `v0.2.0` TO `v0.3.0`

Adjust you entity following the subsequent PR notes, then remember to run a migration diff and run it with:

```shell
php bin/console doctrine:migrations:diff
php bin/console doctrine:migrations:migrate
```

### Codebase

#### Add tags to a contact (#40)

##### TL;DR
The `messenger.default_bus` is now passed to both `webgriffe.sylius_active_campaign_plugin.message_handler.contact.update` and `webgriffe.sylius_active_campaign_plugin.message_handler.contact.create` services.

##### BC Breaks

###### Changed
- [BC] The number of required arguments for Webgriffe\SyliusActiveCampaignPlugin\MessageHandler\Contact\ContactUpdateHandler#__construct() increased from 3 to 4
- [BC] The number of required arguments for Webgriffe\SyliusActiveCampaignPlugin\MessageHandler\Contact\ContactCreateHandler#__construct() increased from 3 to 4

#### Add Contact List Subscription Message and Handler (#41)

##### TL;DR
The `listSubscriptionStatus` property has been added to the `ChannelCustomer` entity, so two new methods (get and set) of that property, have been added to the `ChannelCustomerInterface`. You can use the `Webgriffe\SyliusActiveCampaignPlugin\Model\ChannelActiveCampaignAwareTrait` to implement these methods.

##### BC Breaks

###### Added
- [BC] Method getListSubscriptionStatus() was added to interface Webgriffe\SyliusActiveCampaignPlugin\Model\ChannelCustomerInterface
- [BC] Method setListSubscriptionStatus() was added to interface Webgriffe\SyliusActiveCampaignPlugin\Model\ChannelCustomerInterface

#### Add Connection factory (#46)

##### TL;DR
The `webgriffe.sylius_active_campaign_plugin.factory.active_campaign.connection` is now passed to `webgriffe.sylius_active_campaign_plugin.mapper.connection` service.

##### BC Breaks

###### Changed
- [BC] The number of required arguments for Webgriffe\SyliusActiveCampaignPlugin\Mapper\ConnectionMapper#__construct() increased from 0 to 1

#### Update lists status subscription from ActiveCampaign (#44)

##### TL;DR
The get method has been added to the `ActiveCampaignResourceClientInterface`. A new app route has been added for a webhook.

##### BC Breaks

###### Added
- [BC] Method get() was added to interface Webgriffe\SyliusActiveCampaignPlugin\Client\ActiveCampaignResourceClientInterface

#### Use FQCN injected for factory services (#47)

##### TL;DR
The FQCN is now injected in all factories.

##### BC Breaks

###### Changed
- [BC] Webgriffe\SyliusActiveCampaignPlugin\Factory\ActiveCampaign\ConnectionFactory#__construct() increased from 0 to 1
- [BC] Webgriffe\SyliusActiveCampaignPlugin\Factory\ActiveCampaign\ContactFactory#__construct() increased from 0 to 1
- [BC] Webgriffe\SyliusActiveCampaignPlugin\Factory\ActiveCampaign\ContactListFactory#__construct() increased from 0 to 1
- [BC] Webgriffe\SyliusActiveCampaignPlugin\Factory\ActiveCampaign\ContactTagFactory#__construct() increased from 0 to 1
- [BC] Webgriffe\SyliusActiveCampaignPlugin\Factory\ActiveCampaign\EcommerceCustomerFactory#__construct() increased from 0 to 1
- [BC] Webgriffe\SyliusActiveCampaignPlugin\Factory\ActiveCampaign\EcommerceOrderDiscountFactory#__construct() increased from 0 to 1
- [BC] Webgriffe\SyliusActiveCampaignPlugin\Factory\ActiveCampaign\EcommerceOrderFactory#__construct() increased from 0 to 1
- [BC] Webgriffe\SyliusActiveCampaignPlugin\Factory\ActiveCampaign\EcommerceOrderProductFactory#__construct() increased from 0 to 1
- [BC] Webgriffe\SyliusActiveCampaignPlugin\Factory\ActiveCampaign\TagFactory#__construct() increased from 0 to 1

## UPGRADE FROM `v0.1.0` TO `v0.2.0`

### Codebase

#### Use findAllToEnqueue method on ChannelsResolver to use the same method of connection exporters (#36)

##### TL;DR
The service `webgriffe.sylius_active_campaign_plugin.resolver.all_enabled_channels` has been replaced with the new service `webgriffe.sylius_active_campaign_plugin.resolver.enqueuable_channels`.

##### BC Breaks

###### Changed
- [BC] Class Webgriffe\SyliusActiveCampaignPlugin\Resolver\AllEnabledChannelsResolver has been deleted
