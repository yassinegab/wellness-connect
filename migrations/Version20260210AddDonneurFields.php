<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260210AddDonneurFields extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add missing fields to donneur table safely';
    }

    public function up(Schema $schema): void
    {
        // Add groupe_sanguin ONLY if it does not exist
        $this->addSql("
            ALTER TABLE donneur 
            ADD groupe_sanguin VARCHAR(3) NOT NULL
        ");

        // Add created_at
        $this->addSql("
            ALTER TABLE donneur 
            ADD created_at DATETIME NOT NULL
        ");

        // Increase phone length
        $this->addSql("
            ALTER TABLE donneur 
            MODIFY telephone VARCHAR(15) NOT NULL
        ");
    }

    public function down(Schema $schema): void
    {
        $this->addSql("ALTER TABLE donneur DROP groupe_sanguin");
        $this->addSql("ALTER TABLE donneur DROP created_at");
        $this->addSql("ALTER TABLE donneur MODIFY telephone VARCHAR(8) NOT NULL");
    }
}
