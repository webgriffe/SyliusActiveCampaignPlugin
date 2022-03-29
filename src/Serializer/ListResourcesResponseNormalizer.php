<?php

declare(strict_types=1);

namespace Webgriffe\SyliusActiveCampaignPlugin\Serializer;

use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Webgriffe\SyliusActiveCampaignPlugin\ValueObject\Response\ListResourcesResponseInterface;
use Webmozart\Assert\Assert;

final class ListResourcesResponseNormalizer implements DenormalizerInterface
{
    public function __construct(
        private DenormalizerInterface $denormalizer
    ) {
    }

    public function denormalize($data, string $type, string $format = null, array $context = [])
    {
        Assert::isArray($data);
        unset($context['type']);

        /** @var string $resourceName */
        $resourceName = $context['resource'];
        $listResourcesKey = $resourceName . 's';
        $fqcnResource = $context['responseType'];
        Assert::string($fqcnResource);

        return new $type($this->denormalizer->denormalize($data[$listResourcesKey], $fqcnResource . '[]', $format, $context));
    }

    public function supportsDenormalization($data, string $type, string $format = null, array $context = []): bool
    {
        return is_array($data) && isset($context['type']) && $context['type'] === ListResourcesResponseInterface::class;
    }
}
