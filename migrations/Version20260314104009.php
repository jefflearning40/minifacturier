<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260314104009 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE customer (id INT AUTO_INCREMENT NOT NULL, first_name_customer VARCHAR(100) NOT NULL, last_name_customer VARCHAR(100) NOT NULL, address_customer VARCHAR(255) DEFAULT NULL, phone_customer VARCHAR(20) DEFAULT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE invoice (id INT AUTO_INCREMENT NOT NULL, number_invoice VARCHAR(50) NOT NULL, description_item VARCHAR(255) NOT NULL, price_item NUMERIC(10, 2) NOT NULL, qty INT NOT NULL, total NUMERIC(10, 2) NOT NULL, sale_date DATE NOT NULL, seller_id INT NOT NULL, customer_id INT NOT NULL, INDEX IDX_906517448DE820D9 (seller_id), INDEX IDX_906517449395C3F3 (customer_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE seller (id INT AUTO_INCREMENT NOT NULL, first_name_seller VARCHAR(100) NOT NULL, last_name_seller VARCHAR(100) NOT NULL, phone_seller VARCHAR(20) DEFAULT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL, available_at DATETIME NOT NULL, delivered_at DATETIME DEFAULT NULL, INDEX IDX_75EA56E0FB7336F0E3BD61CE16BA31DBBF396750 (queue_name, available_at, delivered_at, id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE invoice ADD CONSTRAINT FK_906517448DE820D9 FOREIGN KEY (seller_id) REFERENCES seller (id)');
        $this->addSql('ALTER TABLE invoice ADD CONSTRAINT FK_906517449395C3F3 FOREIGN KEY (customer_id) REFERENCES customer (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE invoice DROP FOREIGN KEY FK_906517448DE820D9');
        $this->addSql('ALTER TABLE invoice DROP FOREIGN KEY FK_906517449395C3F3');
        $this->addSql('DROP TABLE customer');
        $this->addSql('DROP TABLE invoice');
        $this->addSql('DROP TABLE seller');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
