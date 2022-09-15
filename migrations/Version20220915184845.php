<?php declare(strict_types = 1);

// phpcs:disable SlevomatCodingStandard.Files.LineLength.LineTooLong

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20220915184845 extends AbstractMigration
{

    public function up(Schema $schema): void // phpcs:ignore SlevomatCodingStandard.Functions.UnusedParameter.UnusedParameter
    {
        $this->addSql('ALTER TABLE contact CHANGE `status` `status` VARCHAR(255) NOT NULL');
    }

    public function down(Schema $schema): void // phpcs:ignore SlevomatCodingStandard.Functions.UnusedParameter.UnusedParameter
    {
        $this->throwIrreversibleMigrationException();
    }

}
