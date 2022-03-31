# First setup

[Return to Usage](03-Usage.md)

Right after installing the plugin, you need to export all the resources to ActiveCampaign if you start from scratch,
persist the ActiveCampaign resource's id on Sylius resource if you already have ActiveCampaign populated, and/or of
course, create and associate at the same time if you start from a mixed case. To do this the plugin offers three
commands to do this:

### The Enqueue Connection Command

The Enqueue Connection Command creates/updates the Sylius channel as connections on ActiveCampaign. To start it you just
need to launch the following command:

```shell
php bin/console webgriffe:active-campaign:enqueue-connection --all
```

But what if you need to export to ActiveCampaign only some Sylius channels? Simply, just override the logic inside the
`findAllToEnqueue` `ChannelRepository`'s method. So, you can, for example, add a boolean property to your channels that
specify if that channel can be exported to ActiveCampaign or not.

Also, remember that this command acts like a "create/update connections on ActiveCampaign" so, if you added a channel,
not from the Sylius Admin you could launch it without any fear, and it will enqueue the new connection creation. The
command offers also a way to specify which channel enqueue by adding the channel-id parameter to it:

```shell
php bin/console webgriffe:active-campaign:enqueue-connection 2
```

You can also launch the command without any arguments, it will ask you automatically.

```shell
php bin/console webgriffe:active-campaign:enqueue-connection
```

### The Enqueue Contact and Ecommerce Customer Command

The Enqueue Contact and Ecommerce Customer Command creates/updates the Sylius Customer as Contacts and Ecommerce
Customers on ActiveCampaign. To start it you just need to launch the following command:

```shell
php bin/console webgriffe:active-campaign:enqueue-contact-and-ecommerce-customer --all
```

_Please_! Remember that the plugin doesn't know which customers have given their consent to the marketing treatment.

It is your responsibility to handle this logic, but you have many simple ways to handle this. For example, you can
choose to export as a contact and an ecommerce customer only if this has accepted the privacy treatment. In this case
you can override the logic inside the `findAllToEnqueue` `CustomerRepository`'s method.

Another case could be to export all the customers as contacts on ActiveCampaign, independently if they have accepted the
marketing treatment or not, but you can specify if they have accepted the marketing treatment on the Ecommerce
Customer's acceptsMarketing field. In this way, you can activate the marketing automation on ActiveCampaign only for
contacts that have given their consent to receive marketing information or others. To do this, you need to decorate the
`webgriffe.sylius_active_campaign_plugin.mapper.ecommerce_customer` service and implements the logic given a customer to
set acceptsMarketing flag or not.

Which way to choose depends on the _laws of privacy and GDPR_ to which you are subject. **Remember that is your
responsibility to choose the right way to handle customers' data**. **Webgriffe does not take any responsibility for
this**.

The Ecommerce customer on ActiveCampaign is a representation of the association between the contact and the connection.
In the same way, the plugin provides an association between Sylius's Channels and Customers. In Sylius Standard there is
no way to associate a customer to a Channel by default so the plugin creates an Ecommerce Customer for every Channel
given a Customer. But if you have personalized this logic or, if you are using Sylius Plus which provides a way to do
it, you probably need to customize this logic. To do it you could decorate the `TODO` service and implements the logic
to return if the Channel-Customer association should be created or not.

Also, remember that this command acts like a "create/update contacts/ecommerce customer on ActiveCampaign" so, if you
added a customer, not from the Sylius Admin or by the shop register form you could launch it without any fear, and it
will enqueue the new contact and/or ecommerce customer creation. The command offers also a way to specify which customer
enqueue by adding the customer-id parameter to it:

```shell
php bin/console webgriffe:active-campaign:enqueue-contact-and-ecommerce-customer 23
```

You can also launch the command without any arguments, it will ask you automatically.

```shell
php bin/console webgriffe:active-campaign:enqueue-contact-and-ecommerce-customer
```

### The Enqueue Ecommerce Order Command

The Enqueue Ecommerce Order Command creates/updates the Sylius Orders/Carts as Ecommerce Order/Abandoned Cart on
ActiveCampaign. To start it you just need to launch the following command:

```shell
php bin/console webgriffe:active-campaign:enqueue-ecommerce-order --all
```

This command allows you to create the history of orders and abandoned carts for every customer/contact. Note that it
creates the orders and carts without the "source" flag enabled. This means that all the automations about carts and
orders wouldn't start. This is probably what is right for you since it is a historian of orders, but you can customize
it.

But what if you need to export to ActiveCampaign only some Sylius Orders? Simply, just override the logic inside the
`findAllToEnqueue` `OrderRepository`'s method. So, you can, for example, exports only orders by some customers.

Also, remember that this command acts like a "create/update ecommerce order on ActiveCampaign" so, if you added an order,
not from the shop checkout you could launch it without any fear, and it will enqueue the new ecommerce order creation. The
command offers also a way to specify which order/cart enqueue by adding the order-id parameter to it:

```shell
php bin/console webgriffe:active-campaign:enqueue-ecommerce-order 65
```

You can also launch the command without any arguments, it will ask you automatically.

```shell
php bin/console webgriffe:active-campaign:enqueue-ecommerce-order
```

[Return to Usage](03-Usage.md)
