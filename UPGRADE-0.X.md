# UPGRADE FROM `v0.1.0` TO `v0.2.0`

## Codebase

### Use findAllToEnqueue method on ChannelsResolver to use the same method of connection exporters (#36)

#### TL;DR
Replaced the argument type of the ChannelsResolver from the `Sylius\Component\Channel\Repository\ChannelRepositoryInterface` to `Webgriffe\SyliusActiveCampaignPlugin\Repository\ActiveCampaignResourceRepositoryInterface`. This should be the same instance anyway.

#### BC Breaks

##### Changed
- [BC] The parameter $channelRepository of Webgriffe\SyliusActiveCampaignPlugin\Resolver\AllEnabledChannelsResolver#__construct() changed from Sylius\Component\Channel\Repository\ChannelRepositoryInterface to a non-contravariant Webgriffe\SyliusActiveCampaignPlugin\Repository\ActiveCampaignResourceRepositoryInterface
