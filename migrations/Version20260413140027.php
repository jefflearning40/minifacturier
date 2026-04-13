<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260413140027 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE invoice_item ADD vat_rate NUMERIC(5, 2) NOT NULL');
        $this->addSql('ALTER TABLE invoice_item ADD CONSTRAINT FK_1DDE477B4584665A FOREIGN KEY (product_id) REFERENCES product (id)');
        $this->addSql('CREATE INDEX IDX_1DDE477B4584665A ON invoice_item (product_id)');
        $this->addSql('ALTER TABLE product DROP vat_rate');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE invoice_item DROP FOREIGN KEY FK_1DDE477B4584665A');
        $this->addSql('DROP INDEX IDX_1DDE477B4584665A ON invoice_item');
        $this->addSql('ALTER TABLE invoice_item DROP vat_rate');
        $this->addSql('ALTER TABLE product ADD vat_rate NUMERIC(5, 2) DEFAULT \'20.00\' NOT NULL');
    }
}
