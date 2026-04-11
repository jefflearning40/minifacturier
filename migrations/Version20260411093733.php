<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260411093733 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE invoice DROP description_item, DROP price_item, DROP qty, DROP total, DROP marque');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE invoice ADD description_item VARCHAR(255) NOT NULL, ADD price_item NUMERIC(10, 2) NOT NULL, ADD qty INT NOT NULL, ADD total NUMERIC(10, 2) NOT NULL, ADD marque VARCHAR(100) DEFAULT NULL');
    }
}
