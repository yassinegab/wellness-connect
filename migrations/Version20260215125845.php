<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260215125845 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE meal ADD user_id INT NOT NULL, CHANGE ai_analysis ai_analysis LONGTEXT DEFAULT NULL');
        $this->addSql('ALTER TABLE meal ADD CONSTRAINT FK_9EF68E9CA76ED395 FOREIGN KEY (user_id) REFERENCES `user` (id)');
        $this->addSql('CREATE INDEX IDX_9EF68E9CA76ED395 ON meal (user_id)');
        $this->addSql('ALTER TABLE rendez_vous CHANGE score_ai score_ai DOUBLE PRECISION DEFAULT NULL');
        $this->addSql('ALTER TABLE user CHANGE roles roles JSON NOT NULL, CHANGE telephone telephone VARCHAR(20) DEFAULT NULL');
        $this->addSql('ALTER TABLE user_well_being_data RENAME INDEX fk_user_id TO IDX_F6B5E75DA76ED395');
        $this->addSql('ALTER TABLE messenger_messages CHANGE delivered_at delivered_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\'');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE meal DROP FOREIGN KEY FK_9EF68E9CA76ED395');
        $this->addSql('DROP INDEX IDX_9EF68E9CA76ED395 ON meal');
        $this->addSql('ALTER TABLE meal DROP user_id, CHANGE ai_analysis ai_analysis LONGTEXT NOT NULL');
        $this->addSql('ALTER TABLE messenger_messages CHANGE delivered_at delivered_at DATETIME DEFAULT \'NULL\' COMMENT \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE rendez_vous CHANGE score_ai score_ai DOUBLE PRECISION DEFAULT \'NULL\'');
        $this->addSql('ALTER TABLE `user` CHANGE roles roles JSON NOT NULL COLLATE `utf8mb4_bin`, CHANGE telephone telephone VARCHAR(20) DEFAULT \'NULL\'');
        $this->addSql('ALTER TABLE user_well_being_data RENAME INDEX idx_f6b5e75da76ed395 TO FK_USER_ID');
    }
}
