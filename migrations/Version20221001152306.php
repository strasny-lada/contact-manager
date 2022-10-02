<?php declare(strict_types = 1);

// phpcs:disable SlevomatCodingStandard.Files.LineLength.LineTooLong

namespace DoctrineMigrations;

use App\Slugger\Checker\ContactSlugCheckerDBAL;
use App\Slugger\ContactSlugger;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;
use Ramsey\Uuid\Uuid;
use Symfony\Component\String\Slugger\AsciiSlugger;

final class Version20221001152306 extends AbstractMigration
{

    public function up(Schema $schema): void // phpcs:ignore SlevomatCodingStandard.Functions.UnusedParameter.UnusedParameter
    {
        $this->connection->setAutoCommit(false);
        $this->connection->connect();
        $this->connection->beginTransaction();

        $this->connection->executeQuery('ALTER TABLE contact CHANGE id id VARCHAR(36) NOT NULL');
        $this->connection->commit();

        $contacts = $this->connection->fetchAllAssociative('SELECT id, firstname, lastname FROM contact ORDER BY id');

        $contactSlugger = new ContactSlugger(
            new AsciiSlugger(),
            new ContactSlugCheckerDBAL($this->connection),
        );

        foreach ($contacts as $contact) {
            $this->connection->beginTransaction();

            $this->connection->executeQuery('UPDATE contact SET id = :newId, slug = :slug WHERE id = :oldId', [
                'oldId' => $contact['id'],
                'newId' => Uuid::uuid4()->toString(),
                'slug' => $contactSlugger->slugify(sprintf(
                    '%s %s',
                    $contact['lastname'],
                    $contact['firstname'],
                )),
            ]);

            $this->connection->commit();
        }
    }

    public function down(Schema $schema): void // phpcs:ignore SlevomatCodingStandard.Functions.UnusedParameter.UnusedParameter
    {
        $this->throwIrreversibleMigrationException();
    }

}
