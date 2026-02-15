<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260210220000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add user_id column to user_well_being_data table';
    }

    public function up(Schema $schema): void
    {
        // add the column
        $this->addSql('ALTER TABLE user_well_being_data ADD user_id INT NOT NULL');

        // add the foreign key constraint
        $this->addSql('ALTER TABLE user_well_being_data ADD CONSTRAINT FK_USER_ID FOREIGN KEY (user_id) REFERENCES user(id)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE user_well_being_data DROP FOREIGN KEY FK_USER_ID');
        $this->addSql('ALTER TABLE user_well_being_data DROP COLUMN user_id');
    }
}
