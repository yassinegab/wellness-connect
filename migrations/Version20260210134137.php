<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260210134137 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE stress_prediction (id INT AUTO_INCREMENT NOT NULL, user_well_being_data_id INT NOT NULL, predicted_stress_type VARCHAR(255) NOT NULL, predicted_label VARCHAR(255) NOT NULL, confidence_score DOUBLE PRECISION NOT NULL, model_version VARCHAR(50) NOT NULL, recommendation LONGTEXT DEFAULT NULL, created_at DATETIME NOT NULL, INDEX IDX_392D7CBFA8115A0D (user_well_being_data_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE `user` (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL COMMENT \'(DC2Type:json)\', password VARCHAR(255) NOT NULL, nom VARCHAR(255) NOT NULL, prenom VARCHAR(255) NOT NULL, telephone VARCHAR(20) DEFAULT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', UNIQUE INDEX UNIQ_8D93D649E7927C74 (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user_well_being_data (id INT AUTO_INCREMENT NOT NULL, work_environment INT NOT NULL, sleep_problems INT NOT NULL, headaches INT NOT NULL, restlessness INT NOT NULL, heartbeat_palpitations INT NOT NULL, low_academic_confidence INT NOT NULL, class_attendance INT NOT NULL, anxiety_tension INT NOT NULL, irritability INT NOT NULL, subject_confidence INT NOT NULL, created_at DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', available_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', delivered_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_75EA56E0FB7336F0E3BD61CE16BA31DBBF396750 (queue_name, available_at, delivered_at, id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE stress_prediction ADD CONSTRAINT FK_392D7CBFA8115A0D FOREIGN KEY (user_well_being_data_id) REFERENCES user_well_being_data (id)');
        $this->addSql('ALTER TABLE analyse_ai DROP rendez_vous_id, DROP notes_ai, CHANGE decision_proposee decision_proposee LONGTEXT NOT NULL, CHANGE created_at created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', CHANGE specialite_recomandee specialite_recommandee VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE analyse_ai ADD CONSTRAINT FK_A896F00A6B899279 FOREIGN KEY (patient_id) REFERENCES `user` (id)');
        $this->addSql('CREATE INDEX IDX_A896F00A6B899279 ON analyse_ai (patient_id)');
        $this->addSql('ALTER TABLE rendez_vous ADD medecin_id INT DEFAULT NULL, ADD date_rendez_vous DATETIME NOT NULL, ADD notes LONGTEXT DEFAULT NULL, DROP medcin_id, CHANGE patient_id patient_id INT DEFAULT NULL, CHANGE score_ai score_ai DOUBLE PRECISION DEFAULT NULL, CHANGE created_at created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', CHANGE updated_at updated_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE rendez_vous ADD CONSTRAINT FK_65E8AA0A6B899279 FOREIGN KEY (patient_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE rendez_vous ADD CONSTRAINT FK_65E8AA0A4F31A84 FOREIGN KEY (medecin_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE rendez_vous ADD CONSTRAINT FK_65E8AA0ACC0FBF92 FOREIGN KEY (hopital_id) REFERENCES hopital (id)');
        $this->addSql('CREATE INDEX IDX_65E8AA0A6B899279 ON rendez_vous (patient_id)');
        $this->addSql('CREATE INDEX IDX_65E8AA0A4F31A84 ON rendez_vous (medecin_id)');
        $this->addSql('CREATE INDEX IDX_65E8AA0ACC0FBF92 ON rendez_vous (hopital_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE analyse_ai DROP FOREIGN KEY FK_A896F00A6B899279');
        $this->addSql('ALTER TABLE rendez_vous DROP FOREIGN KEY FK_65E8AA0A6B899279');
        $this->addSql('ALTER TABLE rendez_vous DROP FOREIGN KEY FK_65E8AA0A4F31A84');
        $this->addSql('ALTER TABLE stress_prediction DROP FOREIGN KEY FK_392D7CBFA8115A0D');
        $this->addSql('DROP TABLE stress_prediction');
        $this->addSql('DROP TABLE `user`');
        $this->addSql('DROP TABLE user_well_being_data');
        $this->addSql('DROP TABLE messenger_messages');
        $this->addSql('DROP INDEX IDX_A896F00A6B899279 ON analyse_ai');
        $this->addSql('ALTER TABLE analyse_ai ADD rendez_vous_id INT NOT NULL, ADD notes_ai LONGTEXT NOT NULL, CHANGE decision_proposee decision_proposee VARCHAR(255) NOT NULL, CHANGE created_at created_at DATETIME NOT NULL, CHANGE specialite_recommandee specialite_recomandee VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE rendez_vous DROP FOREIGN KEY FK_65E8AA0ACC0FBF92');
        $this->addSql('DROP INDEX IDX_65E8AA0A6B899279 ON rendez_vous');
        $this->addSql('DROP INDEX IDX_65E8AA0A4F31A84 ON rendez_vous');
        $this->addSql('DROP INDEX IDX_65E8AA0ACC0FBF92 ON rendez_vous');
        $this->addSql('ALTER TABLE rendez_vous ADD medcin_id INT NOT NULL, DROP medecin_id, DROP date_rendez_vous, DROP notes, CHANGE patient_id patient_id INT NOT NULL, CHANGE score_ai score_ai DOUBLE PRECISION NOT NULL, CHANGE created_at created_at DATETIME NOT NULL, CHANGE updated_at updated_at DATETIME NOT NULL');
    }
}
