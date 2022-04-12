# UPGRADE FROM `v0.1.0` TO `v0.2.0`

## Codebase

### Use findAllToEnqueue method on ChannelsResolver to use the same method of connection exporters (#36)

#### TL;DR
The service `webgriffe.sylius_active_campaign_plugin.resolver.all_enabled_channels` has been replaced with the new service `webgriffe.sylius_active_campaign_plugin.resolver.enqueuable_channels`.

#### BC Breaks

##### Changed
- [BC] Class Webgriffe\SyliusActiveCampaignPlugin\Resolver\AllEnabledChannelsResolver has been deleted
