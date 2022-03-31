# Automation examples

[Return to Usage](03-Usage.md)

### Subscribe automatically to a list

This guide shows you how to associate a new contact to an ActiveCampaign's list automatically. First enter on your
ActiveCampaign app and create a new `Automation`. Select `Start from Scratch` from the automation templates.
Select `Tag is added` as trigger. Enter the `sylius-customer` tag (this is automatically added by ActiveCampaign on each
Ecommerce Customer created). Select how many times the automation should run and then check
the `Segment the contacts entering this automations`. In the first select choose the `Has opted in to marketing` (you
can search it) and select for which connections the contact should have opted in. You can add any other conditions as
you need. Then `Save Start`.

Then `Add a New Action`. Choose the `Contacts` tab on the left and then the `Subscribe action`. Select the list to which
associate the contact and then `Save`. Save the automation and active it.

_Congratulations!_ You have completed subscribe to a list automation.

### Email the customer for a new Abandoned Cart

This guide shows you how to email the customer for his new abandoned cart. Create new `automation` and select
`Start from Scratch` as a template. Select `Contact abandons an ecommerce cart` as starting trigger. Choose which
connection to start the automation. You can add any filter on the product, category, or cart you want. Select how many
times the action should run. Then check the `Segment the contacts entering this automation`.
Select `Has opted in to marketing` in the first select and then choose for which connections the marketing should be
accepted in the second. Obviously, you can add any other conditions. The `Save Start`.

Add a new Action by selecting `Send an email` inside the `Sending Options` tab on the left. Select the email template or
create a new one then click on `Save`. Remember to activate your automation.

Now, you need to trigger the abandoned cart on Sylius. For this purpose, you can use the webgriffe:active-campaign:
enqueue-ecommerce-abandoned-cart command.

```shell
php bin/console webgriffe:active-campaign:enqueue-ecommerce-abandoned-cart
```

To make abandoned cart automations feature works automatically the following is the suggested crontab:

```shell
0   *   *  *  *  /path/to/sylius/bin/console -e prod -q webgriffe:active-campaign:enqueue-ecommerce-abandoned-cart
```

By default, the enqueue ecommerce abandoned cart command trigger new abandoned cart not updated for a day. This is
because the expired carts' remover on Sylius will remove the carts after two days if they are not updated. You can anyway
adjust the time for abandoned cart by editing the `webgriffe_sylius_active_campaign.cart_becomes_abandoned_period`
parameter.

_Congratulations!_ You have created an Abandoned cart automation!

[Return to Usage](03-Usage.md)
