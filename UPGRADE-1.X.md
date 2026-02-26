# Upgrade plugin guide

## Upgrade from version v0.x to v1.x

The v1 is now compatible with Sylius 2.x, so you need to update your Sylius version to 2.x before upgrading the plugin. Some changes not listed here may be required, so please refer to the Sylius 2.x upgrade guide for more details.

- The route `@WebgriffeSyliusActiveCampaignPlugin/config/app/config.yaml` has been renamed to `@WebgriffeSyliusActiveCampaignPlugin/config/config.yaml`.
- The route `@WebgriffeSyliusActiveCampaignPlugin/config/app_routing.yml` has been renamed to `@WebgriffeSyliusActiveCampaignPlugin/config/app_routing.yaml`.
- The migrations are now stored inside the plugin in `src/Migrations`. These should be idempotent, so if the changes made by these migrations are already present, they will do nothing.
- Templates paths have changed
