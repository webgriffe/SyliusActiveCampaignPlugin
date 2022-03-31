# Ecommerce Order URL

* Status: proposed
* Date: 2022-03-30

## Context and Problem Statement

The Ecommerce Order entity on Active Campaign has a field called "orderUrl", in which it's possible to save a URL for the Order.

On Active Campaign (AC) there are different usages for this URL:
* It could be used in the abandoned cart email to let the Customer recover its Cart
* It is showed in the admin of AC as the link for abandoned carts of a Customer
* It is showed in the admin of AC as the link for orders of a Customer

On Sylius the Order could have different URLs:
* While the Order is still a Cart, the URL of the Order could be the link to the cart page. This URL is meaningful only for the User.
* Link to the Order on the admin section. This is useful only for the Admin.
* Link to the Order on the account section. This is useful only for the User.

## Decision Drivers

* Driver 1: There is only one field on the Ecommerce Order in which you can store an URL. If there were two, it would have been possible to store both admin and user URLs. 
* Driver 2: Admin users can easily find a specific Order on Sylius' admin that is shown on AC, because this one shows the Order number. 

## Considered Options

### [option 1]

Always populate the "orderUrl" field with User related URLs. 

* Good, because the abandoned cart can have a button that allow the customer to revover its Cart.
* Good, because it will be possible to create comunications to the User that contains references, in particular the URL, of the Order.
* Bad, because the Admin don't have a link that points to the Order page on Sylius' admin.

### [option 2]

Always populate the "orderUrl" field with Admin related URLs. 

* Good, because the Admin always have a link that points to the Order page on Sylius' admin.
* Bad, because while the Order is still a Cart, the Admin cannot use that URL (it points to a page that is meaningful only for the User).
* Bad, because it's not possible to add a button with the link to recover the Cart in abandonde cart emails.
* Bad, because it's not possible to create mails that will be sent to the User that contain the URL of the Order.

### [option 3]

Populate the "orderUrl" field with a User related URL while the Order is still a Cart (eg: the URL to the cart page), and populate it with the Admin related URL when the Cart become an Order (the URL to the Order on the Admin section).

* Good, because the abandoned cart can have a button that allows the customer to revover its Cart.
* Good, because the Admin will have a link that points to the Order page on Sylius' admin.
* Bad, because it's not possible to create mails that will be sent to the User that contain the URL of the Order.
* Bad, because the field changes the target of the information that it stores. Initially it's useful for the User but in the end it will become useful for the Admin. This could create some confusion.

## Decision Outcome

TODO

Chosen option: "[option 1]", because [justification. e.g., only option, which meets k.o. criterion decision driver | which resolves force force | … | comes out best (see below)].

## References

* [Ecommerce Order API](https://developers.activecampaign.com/reference/create-order)

