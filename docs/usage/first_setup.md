---
title: First setup
layout: page
nav_order: 1
parent: Usage
---

# First setup

Right after installing the plugin, you need to export all the resources to ActiveCampaign if you start from scratch,
persist the ActiveCampaign resource's id on Sylius resource if you already have ActiveCampaign populated, and/or of
course, create and associate at the same time if you start from a mixed case. To do this the plugin offers different
commands to reach this scope.

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

### The Enqueue Webhook Command

If you want to maintain updated the list subscriptions status of you customers on Sylius you should enable a webhook for the updates of these subscriptions on ActiveCampaign.
You could obviously create the webhook manually from the AC's dashboard, but you can also use our command:

```shell
php bin/console webgriffe:active-campaign:enqueue-webhook --all
```

This command will create the webhook for all the channels that have an ActiveCampaign list id not nullable. You can also
launch the previous command with argument the id of the channel for which create a list status update webhook.

> **NOTE!** Be sure to add the `webgriffe_sylius_active_campaign_list_status_webhook` route to your app before launch this command, otherwise the route to call from the webhook could not be resolved.

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
it, you probably need to customize this logic. To do it you could decorate the
`Webgriffe\SyliusActiveCampaignPlugin\Resolver\CustomerChannelsResolverInterface` service and implements the logic
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

### The Enqueue Contact Tags Adder Command

If you start from scratch with ActiveCampaign it is probably that you need to add some tags to all of your customers/contacts.
You can use the Enqueue Contact Tags Adder Command to reach this scope:

```shell
php bin/console webgriffe:active-campaign:enqueue-contact-tags-adder --all
```

This command will enqueue to add tags to all the ActiveCampaign's enabled customers of you app. You can also
launch the previous command with argument the id of the customer for which add the tags.

### The Enqueue Contact Lists Subscription Command

If you start from scratch with ActiveCampaign it is probably that you want to subscribe massively all your customers/contacts to the properly Channel's lists.
You can use the Enqueue Contact Lists Subscription Command to reach this scope:

```shell
php bin/console webgriffe:active-campaign:enqueue-contact-lists-subscription --all
```

This command will enqueue all contact lists subscription for all the customers enabled to export them on ActiveCampaign. You can also
launch the previous command with argument the id of the customer for which subscribe to lists.

### The Update Contact Lists Subscription Command

Alternatively to the previous command, if you start from an already populated status on ActiveCampaign, it is probably that you want to update massively all your customers/contacts subscription to Channel's lists on you Sylius store.
This will avoid subscribe to a list when not explicitly requested. You can use the Update Contact Lists Subscription Command to reach this scope:

```shell
php bin/console webgriffe:active-campaign:update-contact-lists-subscription --all
```

This command will update all contact lists subscription for all the customers enabled to export them on ActiveCampaign. You can also
launch the previous command with argument the id of the customer for which update subscription to lists.

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

Also, remember that this command acts like a "create/update ecommerce order on ActiveCampaign" so, if you added an
order, not from the shop checkout you could launch it without any fear, and it will enqueue the new ecommerce order
creation. The command offers also a way to specify which order/cart enqueue by adding the order-id parameter to it:

```shell
php bin/console webgriffe:active-campaign:enqueue-ecommerce-order 65
```

You can also launch the command without any arguments, it will ask you automatically.

```shell
php bin/console webgriffe:active-campaign:enqueue-ecommerce-order
```
