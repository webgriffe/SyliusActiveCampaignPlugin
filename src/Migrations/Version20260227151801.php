<?php

declare(strict_types=1);

namespace Webgriffe\SyliusActiveCampaignPlugin\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * @psalm-api
 */
final class Version20260227151801 extends AbstractMigration
{
    #[\Override]
    public function up(Schema $schema): void
    {
        if (!$schema->getTable('webgriffe_sylius_active_campaign_channel_customer')->hasIndex('channel_customer_idx')) {
            $this->addSql('CREATE UNIQUE INDEX channel_customer_idx ON webgriffe_sylius_active_campaign_channel_customer (channel_id, customer_id)');
        }
    }

    #[\Override]
    public function down(Schema $schema): void
    {
        if ($schema->getTable('webgriffe_sylius_active_campaign_channel_customer')->hasIndex('channel_customer_idx')) {
            $this->addSql('DROP INDEX channel_customer_idx ON webgriffe_sylius_active_campaign_channel_customer');
        }
    }
}
