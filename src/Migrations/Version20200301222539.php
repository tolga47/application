<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200301222539 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE `order`');
        $this->addSql('ALTER TABLE product CHANGE address address VARCHAR(255) DEFAULT NULL, CHANGE shipping_date shipping_date DATE DEFAULT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE `order` (id INT AUTO_INCREMENT NOT NULL, order_code VARCHAR(255) NOT NULL COLLATE utf8mb4_unicode_ci, productid INT NOT NULL, quantity INT NOT NULL, address VARCHAR(255) NOT NULL COLLATE utf8mb4_unicode_ci, shipping_date DATE NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE product CHANGE address address VARCHAR(255) NOT NULL COLLATE utf8mb4_unicode_ci, CHANGE shipping_date shipping_date DATE NOT NULL');
    }
}
