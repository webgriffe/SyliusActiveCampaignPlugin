# Events

[Return to Usage](03-Usage.md)

After the initial setup, the plugin will automatically maintain in sync ActiveCampaign and Sylius resources thanks to
Sylius's resources events.

### Channel events

The plugin listen for these channel events:

- `sylius.channel.post_create`
- `sylius.channel.post_update`
- `sylius.channel.post_delete`

The first two will lead to a Connection Create/Update. The last one will lead to a Connection Remove.

### Customer events

The plugin listen for these customer events:

- `sylius.customer.post_register`
- `sylius.customer.post_create`
- `sylius.customer.post_update`
- `sylius.customer.post_delete`

The first three will lead to a Contact/Ecommerce Customer Create/Update. The last one will lead to a
Contact/EcommerceCustomer Remove.

### Ecommerce Order events

The plugin listen for these order events:

- `sylius.order.post_create`
- `sylius.order.post_update`
- `sylius.order.post_complete`
- `sylius.order.post_delete`

The first three will lead to a Ecommerce Order/Abandoned Cart Create/Update. The last one will lead to a Ecommerce
Order/Abandoned Cart Remove. The EcommerceOrderEnqueuer will automatically check for order status. If the order is a
cart or a order in state new or fulfilled it will create an EcommerceOrder Create or Update message. Otherwise, if the
order state is canceled, it will create a EcommerceOrderRemove message and then remove the ActiveCampaign Ecommerce
Order's id on the Sylius order if it is present.

[Return to Usage](03-Usage.md)
