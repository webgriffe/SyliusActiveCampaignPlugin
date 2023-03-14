<?php

declare(strict_types=1);

namespace Webgriffe\SyliusActiveCampaignPlugin\Model;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Sylius\Component\Core\Model\ChannelInterface;

trait CustomerActiveCampaignAwareTrait
{
    /**
     * @var Collection<ChannelCustomerInterface>|ChannelCustomerInterface[]
     *
     * @ORM\OneToMany(targetEntity="Webgriffe\SyliusActiveCampaignPlugin\Model\ChannelCustomerInterface", mappedBy="customer")
     */
    protected $channelCustomers;

    public function __construct()
    {
        parent::__construct();
        $this->channelCustomers = new ArrayCollection();
    }

    /** @return Collection<ChannelCustomerInterface>|ChannelCustomerInterface[] */
    public function getChannelCustomers()
    {
        return $this->channelCustomers;
    }

    public function getChannelCustomerByChannel(ChannelInterface $channel): ?ChannelCustomerInterface
    {
        foreach ($this->getChannelCustomers() as $channelCustomer) {
            if ($channelCustomer->getChannel() === $channel) {
                return $channelCustomer;
            }
        }

        return null;
    }

    public function addChannelCustomer(ChannelCustomerInterface $channelCustomer): void
    {
        if ($this->channelCustomers->contains($channelCustomer)) {
            return;
        }
        $this->channelCustomers->add($channelCustomer);
    }

    public function removeChannelCustomer(ChannelCustomerInterface $channelCustomer): void
    {
        if (!$this->channelCustomers->contains($channelCustomer)) {
            return;
        }
        $this->channelCustomers->removeElement($channelCustomer);
    }
}
