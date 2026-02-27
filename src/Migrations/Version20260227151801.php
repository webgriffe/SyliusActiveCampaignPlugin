<?php

declare(strict_types=1);

namespace Webgriffe\SyliusActiveCampaignPlugin\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260227151801 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql('CREATE UNIQUE INDEX channel_customer_idx ON webgriffe_sylius_active_campaign_channel_customer (channel_id, customer_id)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP INDEX channel_customer_idx ON webgriffe_sylius_active_campaign_channel_customer');
    }
}
