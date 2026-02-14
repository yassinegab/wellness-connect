<?php

namespace App\Command;

use Doctrine\DBAL\Connection;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:fix-database-roles',
    description: 'Corriger les rôles JSON dans la base de données',
)]
class FixDatabaseRolesCommand extends Command
{
    public function __construct(
        private Connection $connection
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $io->title('🔧 Correction des rôles utilisateurs');

        try {
            // Corriger les valeurs NULL en fonction du user_role
            $sql = "
                UPDATE user 
                SET roles = CASE 
                    WHEN user_role = 'PATIENT' THEN :roleUser
                    WHEN user_role = 'MEDECIN' THEN :roleMedecin
                    WHEN user_role = 'ADMIN' THEN :roleAdmin
                    ELSE :roleUser
                END
                WHERE roles IS NULL
            ";
            
            $count = $this->connection->executeStatement($sql, [
                'roleUser' => json_encode(['ROLE_USER']),
                'roleMedecin' => json_encode(['ROLE_MEDECIN']),
                'roleAdmin' => json_encode(['ROLE_ADMIN'])
            ]);

            $io->success("✅ $count utilisateur(s) corrigé(s) avec succès !");

            // Afficher les utilisateurs mis à jour
            $users = $this->connection->fetchAllAssociative(
                "SELECT id, nom, prenom, email, user_role, roles FROM user ORDER BY id"
            );
            
            if (!empty($users)) {
                $io->section('📋 Liste des utilisateurs :');
                $rows = [];
                foreach ($users as $user) {
                    $rows[] = [
                        $user['id'],
                        $user['nom'] . ' ' . $user['prenom'],
                        $user['email'],
                        $user['user_role'],
                        $user['roles']
                    ];
                }
                $io->table(['ID', 'Nom', 'Email', 'User Role', 'Roles JSON'], $rows);
            }

            return Command::SUCCESS;

        } catch (\Exception $e) {
            $io->error('❌ Erreur lors de la correction : ' . $e->getMessage());
            return Command::FAILURE;
        }
    }
}