<?php

declare(strict_types=1);

namespace Webgriffe\SyliusActiveCampaignPlugin\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * @psalm-api
 */
final class Version20260120104433 extends AbstractMigration
{
    #[\Override]
    public function up(Schema $schema): void
    {
        if (!$schema->hasTable('webgriffe_sylius_active_campaign_channel_customer')) {
            $this->addSql('CREATE TABLE webgriffe_sylius_active_campaign_channel_customer (id INT AUTO_INCREMENT NOT NULL, channel_id INT NOT NULL, customer_id INT NOT NULL, active_campaign_id INT NOT NULL, list_subscription_status INT DEFAULT NULL, INDEX IDX_D5B7B0A172F5A1AA (channel_id), INDEX IDX_D5B7B0A19395C3F3 (customer_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET UTF8 COLLATE `UTF8_unicode_ci` ENGINE = InnoDB');
            $this->addSql('ALTER TABLE webgriffe_sylius_active_campaign_channel_customer ADD CONSTRAINT FK_D5B7B0A172F5A1AA FOREIGN KEY (channel_id) REFERENCES sylius_channel (id) ON DELETE CASCADE');
            $this->addSql('ALTER TABLE webgriffe_sylius_active_campaign_channel_customer ADD CONSTRAINT FK_D5B7B0A19395C3F3 FOREIGN KEY (customer_id) REFERENCES sylius_customer (id) ON DELETE CASCADE');
        }

        if (!$schema->getTable('sylius_channel')->hasColumn('active_campaign_id')) {
            $this->addSql('ALTER TABLE sylius_channel ADD active_campaign_id INT DEFAULT NULL');
        }
        if (!$schema->getTable('sylius_channel')->hasColumn('active_campaign_list_id')) {
            $this->addSql('ALTER TABLE sylius_channel ADD active_campaign_list_id INT DEFAULT NULL');
        }
        if (!$schema->getTable('sylius_customer')->hasColumn('active_campaign_id')) {
            $this->addSql('ALTER TABLE sylius_customer ADD active_campaign_id INT DEFAULT NULL');
        }
        if (!$schema->getTable('sylius_order')->hasColumn('active_campaign_id')) {
            $this->addSql('ALTER TABLE sylius_order ADD active_campaign_id INT DEFAULT NULL');
        }
    }

    #[\Override]
    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE webgriffe_sylius_active_campaign_channel_customer DROP FOREIGN KEY FK_D5B7B0A172F5A1AA');
        $this->addSql('ALTER TABLE webgriffe_sylius_active_campaign_channel_customer DROP FOREIGN KEY FK_D5B7B0A19395C3F3');
        $this->addSql('DROP TABLE webgriffe_sylius_active_campaign_channel_customer');
        $this->addSql('ALTER TABLE sylius_channel DROP active_campaign_id, DROP active_campaign_list_id');
        $this->addSql('ALTER TABLE sylius_customer DROP active_campaign_id');
        $this->addSql('ALTER TABLE sylius_order DROP active_campaign_id');
    }
}
