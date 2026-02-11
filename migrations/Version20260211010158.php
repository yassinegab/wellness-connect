<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260211010158 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE user DROP email, DROP password, DROP telephone, DROP role, DROP age, DROP poids, DROP taille, DROP handicap, CHANGE id id INT AUTO_INCREMENT NOT NULL, CHANGE nom nom VARCHAR(255) NOT NULL, CHANGE prenom prenom VARCHAR(255) NOT NULL, CHANGE sexe sexe VARCHAR(255) NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE user ADD email VARCHAR(30) NOT NULL, ADD password VARCHAR(30) NOT NULL, ADD telephone VARCHAR(30) NOT NULL, ADD role VARCHAR(30) NOT NULL, ADD age INT NOT NULL, ADD poids FLOAT NOT NULL, ADD taille FLOAT NOT NULL, ADD handicap VARCHAR(30) NOT NULL, CHANGE id id INT NOT NULL, CHANGE nom nom VARCHAR(30) NOT NULL, CHANGE prenom prenom VARCHAR(30) NOT NULL, CHANGE sexe sexe VARCHAR(30) NOT NULL');
    }
}
