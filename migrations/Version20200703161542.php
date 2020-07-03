<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200703161542 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE subscription DROP FOREIGN KEY FK_A3C664D39D86650F');
        $this->addSql('DROP INDEX idx_a3c664d39d86650f ON subscription');
        $this->addSql('CREATE INDEX IDX_A3C664D3A76ED395 ON subscription (user_id)');
        $this->addSql('ALTER TABLE subscription ADD CONSTRAINT FK_A3C664D39D86650F FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE subscription_payment DROP FOREIGN KEY FK_1E3D6496857C9F24');
        $this->addSql('DROP INDEX idx_1e3d6496857c9f24 ON subscription_payment');
        $this->addSql('CREATE INDEX IDX_1E3D64969A1887DC ON subscription_payment (subscription_id)');
        $this->addSql('ALTER TABLE subscription_payment ADD CONSTRAINT FK_1E3D6496857C9F24 FOREIGN KEY (subscription_id) REFERENCES subscription (id)');
        $this->addSql('ALTER TABLE user CHANGE roles roles LONGTEXT NOT NULL COMMENT \'(DC2Type:json)\'');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE subscription DROP FOREIGN KEY FK_A3C664D3A76ED395');
        $this->addSql('DROP INDEX idx_a3c664d3a76ed395 ON subscription');
        $this->addSql('CREATE INDEX IDX_A3C664D39D86650F ON subscription (user_id)');
        $this->addSql('ALTER TABLE subscription ADD CONSTRAINT FK_A3C664D3A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE subscription_payment DROP FOREIGN KEY FK_1E3D64969A1887DC');
        $this->addSql('DROP INDEX idx_1e3d64969a1887dc ON subscription_payment');
        $this->addSql('CREATE INDEX IDX_1E3D6496857C9F24 ON subscription_payment (subscription_id)');
        $this->addSql('ALTER TABLE subscription_payment ADD CONSTRAINT FK_1E3D64969A1887DC FOREIGN KEY (subscription_id) REFERENCES subscription (id)');
        $this->addSql('ALTER TABLE user CHANGE roles roles LONGTEXT CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_bin`');
    }
}
