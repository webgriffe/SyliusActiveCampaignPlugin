<?php

declare(strict_types=1);

namespace Webgriffe\SyliusActiveCampaignPlugin\Twig;

use Sylius\Component\Customer\Context\CustomerContextInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

final class CustomerExtension extends AbstractExtension
{
    public function __construct(
        private CustomerContextInterface $customerContext
    ) {
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('webgriffe_active_campaign_get_customer_email', [$this, 'getCustomerEmail']),
        ];
    }

    public function getCustomerEmail(): ?string
    {
        $customer = $this->customerContext->getCustomer();
        if ($customer === null) {
            return null;
        }

        return $customer->getEmail();
    }
}
