# Usage

[Return to Summary main page](README.md)

> This plugin makes use of the two components of _Symfony_ [Messenger][symfony_messenger] and [Serializer][symfony_serializer].
> It is highly recommended to have a minimum knowledge of these two components in order to understand how this integration works.

This plugin is, basically, a simple resource exporter from _Sylius_ to _ActiveCampaign_. The behavior of the plugin for
each resource can be summarized as follows:

- A property containing the resource id of ActiveCampaign is added to the corresponding resource on Sylius.
- Each resource has a corresponding enqueuer. The enqueuer takes care of queuing a message on the Symfony Messenger bus.
  To decide which event to queue, the enqueuer first looks to see if the ActiveCampaign id persisted on the Sylius
  resource is present, if this is the case an update event is queued. Either, it searches for an ActiveCampaign resource
  that is already present through the use of some unique properties. If a resource is founded, the ActiveCampaign ID of
  that resource is persisted on the Sylius resource property and an update event is queued. Finally, if none of the
  above has occurred, a resource creation on ActiveCampaign event is queued.
- After the first installation and/or after each resource creation/modification/removal event on Sylius, the enqueuer is
  called so a proper Message is dispatched to the Symfony Messenger.
- For each ActiveCampaign resource message there is a Create, Edit, and Remove Handler. These handlers are responsible
  for mapping the Sylius resource to an ActiveCampaign resource, sending it to the ActiveCampaign WS, and then reading
  the response. Of course, each of the handlers is slightly different from this standard, for example: currently, only
  the create returns the response from the WS to persist the id on the Sylius resource, or the remove handler does not
  map the Sylius resource as it has none need. Communication with the WS of ActiveCampaign takes place thanks to the
  Symfony Serializer component.

The 4 managed ActiveCampaign resources are the following:

- Contact
- Connection
- Ecommerce Customer
- Ecommerce Order/Abandoned Cart

### GDPR

### Contact

The ActiveCampaign's Contact is the equivalent for the Sylius Customer. It is the more "customizable" resource thanks to
the FieldValues properties. You don't need to decorate all the ContactMapper to add a custom field collected in your
store forms. You could simply listen for the `TODO` event.

Before create the resource on ActiveCampaign, the ContactEnqueuer queries for a corresponding contact with the
same `email`.

### Connection

The ActiveCampaign's Connection is the equivalent for the Sylius Channel. We have opted to this way instead of making
only one connection to allow the more flexibility use on ActiveCampaign's integrations.

Before create the resource on ActiveCampaign, the ConnectionEnqueuer queries for a corresponding connection with the
same `service` (put static to `sylius`) and `externalid` (the channel's code).

### Ecommerce Customer

There is no ActiveCampaign's Ecommerce Customer equivalent on Sylius Standard. So, the plugin offer a simple way to add
a new Sylius resource `ChannelCustomer` that is a simple Channel - Customer association. The ActiveCampaign Ecommerce
Customer's id is persisted on this entity. By default, the Ecommerce Customer is created by a Customer and associate it
with all the Channels. But, especially if you use Sylius Plus, you may not want to do this. In this case you just needs
to decorate the `TODO` service and implement it with your custom logic.

Before create the resource on ActiveCampaign, the EcommerceCustomerEnqueuer queries for a corresponding ecommerce
customer with the same `email` and `connectionid` (the channel's code).

### Ecommerce Order/Abandoned Cart

The ActiveCampaign's Ecommerce Order is the equivalent for the Sylius Order. In addition, as done on Sylius, The
Abandoned Cart is the same entity of the Ecommerce Order, so also the Abandoned Cart is related to the Sylius Order.

Before create the resource on ActiveCampaign, the EcommerceOrderEnqueuer queries for a corresponding ecommerce order
with the same `email` and `externalid` (the order's id)/`externalcheckoutid` (the cart's id) based on the state of the
order (cart or different).


[symfony_messenger]: https://symfony.com/doc/current/messenger.html

[symfony_serializer]: https://symfony.com/doc/current/serializer.html
