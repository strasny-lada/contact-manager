<?php declare(strict_types = 1);

// phpcs:disable SlevomatCodingStandard.Files.LineLength.LineTooLong

namespace DoctrineMigrations;

use App\Entity\ContactStatus;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20220915183317 extends AbstractMigration
{

    public function up(Schema $schema): void // phpcs:ignore SlevomatCodingStandard.Functions.UnusedParameter.UnusedParameter
    {
        $this->addSql('ALTER TABLE contact ADD `status` VARCHAR(255) DEFAULT NULL AFTER slug');

        $contacts = $this->connection->query('SELECT id FROM contact')->fetchAll();

        foreach ($contacts as $contact) {
            $this->addSql('UPDATE contact SET `status` = :activeStatus WHERE id = :contactId', [
                'activeStatus' => ContactStatus::ACTIVE->value,
                'contactId' => $contact['id'],
            ]);
        }
    }

    public function down(Schema $schema): void // phpcs:ignore SlevomatCodingStandard.Functions.UnusedParameter.UnusedParameter
    {
        $this->throwIrreversibleMigrationException();
    }

}
