<?php

declare(strict_types=1);

namespace Webgriffe\SyliusActiveCampaignPlugin\Model\ActiveCampaign;

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

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getUrl(): string
    {
        return $this->url;
    }

    public function setUrl(string $url): void
    {
        $this->url = $url;
    }

    public function getEvents(): array
    {
        return $this->events;
    }

    public function setEvents(array $events): void
    {
        $this->events = $events;
    }

    public function getSources(): array
    {
        return $this->sources;
    }

    public function setSources(array $sources): void
    {
        $this->sources = $sources;
    }

    public function getListId(): ?int
    {
        return $this->listId;
    }

    public function setListId(?int $listId): void
    {
        $this->listId = $listId;
    }
}
