<?php

declare(strict_types=1);

namespace Webgriffe\SyliusActiveCampaignPlugin\Model\ActiveCampaign;

interface WebhookInterface extends ResourceInterface
{
    public function getName(): string;

    public function setName(string $name): void;

    public function getUrl(): string;

    public function setUrl(string $url): void;

    /** @return string[] */
    public function getEvents(): array;

    /** @param string[] $events */
    public function setEvents(array $events): void;

    /** @return string[] */
    public function getSources(): array;

    /** @param string[] $sources */
    public function setSources(array $sources): void;

    public function getListId(): ?int;

    public function setListId(?int $listId): void;
}
