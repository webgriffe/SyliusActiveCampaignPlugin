<?php

declare(strict_types=1);

namespace  Webgriffe\SyliusActiveCampaignPlugin\Form\Extension;

use Sylius\Bundle\ChannelBundle\Form\Type\ChannelType;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

final class ChannelTypeExtension extends AbstractTypeExtension
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('activeCampaignListId', TextType::class, [
                'label' => 'webgriffe_sylius_active_campaign.ui.list_id',
            ])
        ;
    }

    public static function getExtendedTypes(): iterable
    {
        return [ChannelType::class];
    }
}
