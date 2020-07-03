<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200703145535 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE subscription (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, subscription_shipping_address_id INT DEFAULT NULL, subscription_billing_address_id INT DEFAULT NULL, status VARCHAR(16) DEFAULT \'new\' NOT NULL, subscription_pack_id INT NOT NULL, started_at DATETIME DEFAULT NULL, updated_at DATETIME DEFAULT NULL, created_at DATETIME NOT NULL, INDEX IDX_A3C664D39D86650F (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE subscription_payment (id INT AUTO_INCREMENT NOT NULL, subscription_id INT DEFAULT NULL, charged_amount INT NOT NULL, date DATE NOT NULL, updated_at DATETIME DEFAULT NULL, created_at DATETIME NOT NULL, INDEX IDX_1E3D6496857C9F24 (subscription_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, username VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_8D93D649F85E0677 (username), UNIQUE INDEX UNIQ_8D93D649E7927C74 (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE subscription ADD CONSTRAINT FK_A3C664D39D86650F FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE subscription_payment ADD CONSTRAINT FK_1E3D6496857C9F24 FOREIGN KEY (subscription_id) REFERENCES subscription (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE subscription_payment DROP FOREIGN KEY FK_1E3D6496857C9F24');
        $this->addSql('ALTER TABLE subscription DROP FOREIGN KEY FK_A3C664D39D86650F');
        $this->addSql('DROP TABLE subscription');
        $this->addSql('DROP TABLE subscription_payment');
        $this->addSql('DROP TABLE user');
    }
}
