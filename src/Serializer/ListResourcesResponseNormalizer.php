<?php

declare(strict_types=1);

namespace Webgriffe\SyliusActiveCampaignPlugin\Serializer;

use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Webgriffe\SyliusActiveCampaignPlugin\ValueObject\Response\ListResourcesResponseInterface;
use Webgriffe\SyliusActiveCampaignPlugin\ValueObject\Response\ResourceResponseInterface;
use Webmozart\Assert\Assert;

final class ListResourcesResponseNormalizer implements DenormalizerInterface
{
    public function __construct(
        private DenormalizerInterface $denormalizer,
    ) {
    }

    #[\Override]
    public function denormalize(mixed $data, string $type, ?string $format = null, array $context = []): mixed
    {
        Assert::classExists($type);
        Assert::isArray($data);
        unset($context['type']);

        /** @var string $resourceName */
        $resourceName = $context['resource'];
        $listResourcesKey = $resourceName . 's';
        /** @var class-string<ResourceResponseInterface>|mixed $fqcnResource */
        $fqcnResource = $context['responseType'];
        Assert::string($fqcnResource);

        /** @psalm-suppress MixedMethodCall */
        return new $type($this->denormalizer->denormalize($data[$listResourcesKey], $fqcnResource . '[]', $format, $context));
    }

    /**
     * @psalm-suppress MethodSignatureMismatch
     */
    #[\Override]
    public function supportsDenormalization($data, string $type, ?string $format = null, array $context = []): bool
    {
        return is_array($data) &&
            array_key_exists('type', $context) &&
            $context['type'] === ListResourcesResponseInterface::class
        ;
    }

    #[\Override]
    public function getSupportedTypes(?string $format): array
    {
        return [
            ListResourcesResponseInterface::class => false,
        ];
    }
}
