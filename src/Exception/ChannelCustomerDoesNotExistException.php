<?php

declare(strict_types=1);

namespace Webgriffe\SyliusActiveCampaignPlugin\Exception;

use InvalidArgumentException;

final class ChannelCustomerDoesNotExistException extends InvalidArgumentException implements ListSubscriptionStatusResolverExceptionInterface
{
}
