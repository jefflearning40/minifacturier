<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260414120328 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE product CHANGE vat_rate vat_rate NUMERIC(5, 2) DEFAULT 20 NOT NULL');
        $this->addSql('ALTER TABLE seller ADD user_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE seller ADD CONSTRAINT FK_FB1AD3FCA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_FB1AD3FCA76ED395 ON seller (user_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE product CHANGE vat_rate vat_rate NUMERIC(5, 2) DEFAULT \'20.00\' NOT NULL');
        $this->addSql('ALTER TABLE seller DROP FOREIGN KEY FK_FB1AD3FCA76ED395');
        $this->addSql('DROP INDEX UNIQ_FB1AD3FCA76ED395 ON seller');
        $this->addSql('ALTER TABLE seller DROP user_id');
    }
}
