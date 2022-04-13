# Usage

[Return to Summary main page](README.md)

> This plugin makes use of the two components of _Symfony_ [Messenger][symfony_messenger] and [Serializer][symfony_serializer].
> It is highly recommended to have a minimum knowledge of these two components to understand how this integration works.

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
  the creation returns the response from the WS to persist the id on the Sylius resource, or the remove handler does not
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
store forms. You could simply listen for
the `webgriffe.sylius_active_campaign_plugin.mapper.customer.pre_add_field_values` event. This event will dispatch
a `Symfony\Component\EventDispatcher\GenericEvent` containing the Customer as subject and an array of `fieldValues` as
argument. Populate this argument with your custom field values. Remember that every item of this array should be an
instance of the `Webgriffe\SyliusActiveCampaignPlugin\Model\ActiveCampaign\FieldValueInterface`.

Before creating the resource on ActiveCampaign, the ContactEnqueuer queries for a corresponding contact with the
same `email`.

By creating or updating a contact you will probably have to add some tags to this contact. If this is your case there is
nothing to more simple than add this tags to your contact 😀. After the creating or the update of a contact a new
Message `ContactTagsAdder`
will be dispatched to the messenger bus. The ContactTagsAdderHandler will use
the `webgriffe.sylius_active_campaign_plugin.resolver.contact_tags`
service to resolve a list of tags to add to the contact. By default this service will return an empty list, but you can
customize it by overriding this service and by making it implements
the `Webgriffe\SyliusActiveCampaignPlugin\Resolver\ContactTagsResolverInterface`. The more beautiful thing is that you
don't have to worry about retrieve the ActiveCampaign tag's id, the plugin will do it for you 🎉. You just have to
return an array of tags as string. The value of each item is the tag that will be added to the contact; then the plugin
will check if the tag exists or not, if not it will create it. Then it will try to add to the contact.

> **NB** The plugin does not "update" the tags of the contact, it will simply add the tags returned from the `ContactTagsResolverInterface`.

### Connection

The ActiveCampaign's Connection is the equivalent of the Sylius Channel. We have opted for this way instead of making
only one connection to allow more flexible use on ActiveCampaign's integrations.

Before creating the resource on ActiveCampaign, the ConnectionEnqueuer queries for a corresponding connection with the
same `service` (static to `sylius`) and `externalid` (the channel's code).

### Ecommerce Customer

There is no ActiveCampaign's Ecommerce Customer equivalent on Sylius Standard. So, the plugin offers a simple way to add
a new Sylius resource `ChannelCustomer` which is a simple Channel-Customer association. The ActiveCampaign Ecommerce
Customer's id is persisted on this entity. By default, the Ecommerce Customer is created by a Customer and associated
with all the Channels. But, especially if you use Sylius Plus, you may not want to do this. In this case, you just need
to decorate the `Webgriffe\SyliusActiveCampaignPlugin\Resolver\CustomerChannelsResolverInterface` service and implement
it with your custom logic.

Before creating the resource on ActiveCampaign, the EcommerceCustomerEnqueuer queries for a corresponding ecommerce
customer with the same `email` and `connectionid` (the channel's code).

### Ecommerce Order/Abandoned Cart

The ActiveCampaign's Ecommerce Order is the equivalent of the Sylius Order. In addition, as done on Sylius, The
Abandoned Cart is the same entity as the Ecommerce Order, so also the Abandoned Cart is related to the Sylius Order.

Before creating the resource on ActiveCampaign, the EcommerceOrderEnqueuer queries for a corresponding ecommerce order
with the same `email` and `externalid` (the order's id)/`externalcheckoutid` (the cart's id) based on the state of the
order (cart or different).

Here are some points/actions to do or to take inspiration from to start using the plugin:

- [First setup](03_A-First_setup.md)
- [Events](03_B-Events.md)
- [Automation examples](03_C-Automation_example.md)

[symfony_messenger]: https://symfony.com/doc/current/messenger.html

[symfony_serializer]: https://symfony.com/doc/current/serializer.html
