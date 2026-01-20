<?php

declare(strict_types=1);

namespace Webgriffe\SyliusActiveCampaignPlugin\Model\ActiveCampaign;

/** @psalm-api */
final class Webhook implements WebhookInterface
{
    /**
     * @param string[] $events
     * @param string[] $sources
     */
    public function __construct(
        private string $name,
        private string $url,
        private array $events = [],
        private array $sources = [],
        private ?int $listId = null,
    ) {
    }

    #[\Override]
    public function getName(): string
    {
        return $this->name;
    }

    #[\Override]
    public function setName(string $name): void
    {
        $this->name = $name;
    }

    #[\Override]
    public function getUrl(): string
    {
        return $this->url;
    }

    #[\Override]
    public function setUrl(string $url): void
    {
        $this->url = $url;
    }

    #[\Override]
    public function getEvents(): array
    {
        return $this->events;
    }

    #[\Override]
    public function setEvents(array $events): void
    {
        $this->events = $events;
    }

    #[\Override]
    public function getSources(): array
    {
        return $this->sources;
    }

    #[\Override]
    public function setSources(array $sources): void
    {
        $this->sources = $sources;
    }

    #[\Override]
    public function getListId(): ?int
    {
        return $this->listId;
    }

    #[\Override]
    public function setListId(?int $listId): void
    {
        $this->listId = $listId;
    }
}
