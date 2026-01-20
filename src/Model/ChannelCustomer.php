<?php

declare(strict_types=1);

namespace Webgriffe\SyliusActiveCampaignPlugin\Model;

use Doctrine\ORM\Mapping as ORM;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\CustomerInterface;

#[ORM\MappedSuperclass]
#[ORM\Table(name: 'webgriffe_sylius_active_campaign_channel_customer')]
#[ORM\UniqueConstraint(name: 'channel_customer_idx', columns: ['channel_id', 'customer_id'])]
class ChannelCustomer implements ChannelCustomerInterface
{
    /** @var mixed */
    #[ORM\Id]
    #[ORM\Column(name: 'id', type: 'integer')]
    #[ORM\GeneratedValue(strategy: 'AUTO')]
    protected $id;

    #[ORM\ManyToOne(targetEntity: ChannelInterface::class)]
    #[ORM\JoinColumn(name: 'channel_id', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')]
    protected ChannelInterface $channel;

    #[ORM\ManyToOne(targetEntity: CustomerInterface::class, inversedBy: 'channelCustomers')]
    #[ORM\JoinColumn(name: 'customer_id', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')]
    protected CustomerInterface $customer;

    #[ORM\Column(name: 'active_campaign_id', type: 'integer', nullable: false)]
    protected int $activeCampaignId;

    #[ORM\Column(name: 'list_subscription_status', type: 'integer', nullable: true)]
    protected ?int $listSubscriptionStatus = null;

    /** @return mixed */
    public function getId()
    {
        return $this->id;
    }

    public function getCustomer(): CustomerInterface
    {
        return $this->customer;
    }

    public function setCustomer(CustomerInterface $customer): void
    {
        $this->customer = $customer;
    }

    public function getChannel(): ChannelInterface
    {
        return $this->channel;
    }

    public function setChannel(ChannelInterface $channel): void
    {
        $this->channel = $channel;
    }

    public function getActiveCampaignId(): int
    {
        return $this->activeCampaignId;
    }

    public function setActiveCampaignId(int $activeCampaignId): void
    {
        $this->activeCampaignId = $activeCampaignId;
    }

    public function getListSubscriptionStatus(): ?int
    {
        return $this->listSubscriptionStatus;
    }

    public function setListSubscriptionStatus(?int $listSubscriptionStatus): void
    {
        $this->listSubscriptionStatus = $listSubscriptionStatus;
    }
}
