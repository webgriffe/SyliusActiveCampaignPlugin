---
title: Receive automation webhooks
layout: page
nav_order: 4
parent: Usage
---

# Receive automation webhooks

It may often be necessary to contact Sylius from ActiveCampaign during an automation.
For example, think of the following scenario: at a particular moment a contact reaches a specific condition such that he
can receive a coupon valid only for him, this coupon code must be sent to him via email by the automation itself.

ActiveCampaign provides the "Webhook: Post a contact data to a URL of your choice" action to achieve this. The plugin
already has a route in place to receive this type of webhook, so use the
URL https://yourdomain.com/webhook/activecampaign/contact-automation-event (where obviously yourdomain.com should be
replaced with your shop domain) for that type of action. By default, when
that route is invoked a `Webgriffe\SyliusActiveCampaignPlugin\Message\Contact\ContactAutomationEvent` type message will
be dispatched to the messenger.
**Note!** There is currently no message handler for this message, you have to create one yourself. Remember that the
message will contain the ID of the Sylius client the automation was invoked for and the ID of the automation itself.
This way, using the same URL, you can configure multiple different webhooks that do different things depending on which 
automation invoked it. An example of a message handler could be the following:

```php
<?php

declare(strict_types=1);

namespace App\MessageHandler;

use InvalidArgumentException;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Repository\CustomerRepositoryInterface;
use Webgriffe\SyliusActiveCampaignPlugin\Message\Contact\ContactAutomationEvent;

final class ContactAutomationEventHandler
{
    private const NEW_COUPON_AUTOMATION_ID = '1';

    public function __construct(
        private CustomerRepositoryInterface $customerRepository,
    ) {
    }

    public function __invoke(ContactAutomationEvent $message): void
    {
        $customerId = $message->getCustomerId();
        /** @var CustomerInterface|null $customer */
        $customer = $this->customerRepository->find($customerId);
        if ($customer === null) {
            throw new InvalidArgumentException(sprintf('Customer with id "%s" does not exists', $customerId));
        }
        if ($message->getAutomationId() === self::NEW_COUPON_AUTOMATION_ID) {
            // Create new coupon for customer and cart promotion...

            return;
        }
        if ($message->getAutomationId() === 'ANOTHER_AUTOMATION_ID') {
            // Do something else...

            return;
        }
    }
}
```

Dont't forget to register the message handler as a service in your `config/services.yaml` file:

```yaml
services:
    ...
    app.message_handler.contact.automation_event:
        class: App\MessageHandler\ContactAutomationEventHandler
        arguments:
            - '@sylius.repository.customer'
        tags:
            - { name: messenger.message_handler }
```
