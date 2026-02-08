<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260204032711 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE analyse_ai (id INT AUTO_INCREMENT NOT NULL, patient_id INT NOT NULL, rendez_vous_id INT NOT NULL, symptomes LONGTEXT NOT NULL, niveau_risque DOUBLE PRECISION NOT NULL, specialite_recomandee VARCHAR(255) NOT NULL, decision_proposee VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL, notes_ai LONGTEXT NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE hopital (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(255) NOT NULL, adresse VARCHAR(255) NOT NULL, tel VARCHAR(255) NOT NULL, service_urgence_dispo TINYINT NOT NULL, latitude DOUBLE PRECISION NOT NULL, longitude DOUBLE PRECISION NOT NULL, capacite INT NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE rendez_vous (id INT AUTO_INCREMENT NOT NULL, patient_id INT NOT NULL, medcin_id INT NOT NULL, hopital_id INT NOT NULL, type_consultation VARCHAR(255) NOT NULL, statut VARCHAR(255) NOT NULL, score_ai DOUBLE PRECISION NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE analyse_ai');
        $this->addSql('DROP TABLE hopital');
        $this->addSql('DROP TABLE rendez_vous');
    }
}
