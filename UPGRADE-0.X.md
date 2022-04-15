# UPGRADE FROM `v0.1.0` TO `v0.X`

## UPGRADE FROM `v0.2.0` TO `v0.3.0`

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
The `listSubscriptionStatus` property has been added to the `ChannelCustomer` entity, so two new methods (get and set) of that property, have been added to the `ChannelCustomerInterface`.

##### BC Breaks

###### Added
- [BC] Method getListSubscriptionStatus() was added to interface Webgriffe\SyliusActiveCampaignPlugin\Model\ChannelCustomerInterface
- [BC] Method setListSubscriptionStatus() was added to interface Webgriffe\SyliusActiveCampaignPlugin\Model\ChannelCustomerInterface

## UPGRADE FROM `v0.1.0` TO `v0.2.0`

### Codebase

#### Use findAllToEnqueue method on ChannelsResolver to use the same method of connection exporters (#36)

##### TL;DR
The service `webgriffe.sylius_active_campaign_plugin.resolver.all_enabled_channels` has been replaced with the new service `webgriffe.sylius_active_campaign_plugin.resolver.enqueuable_channels`.

##### BC Breaks

###### Changed
- [BC] Class Webgriffe\SyliusActiveCampaignPlugin\Resolver\AllEnabledChannelsResolver has been deleted
