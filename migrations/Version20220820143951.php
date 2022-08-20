<?php declare(strict_types = 1);

// phpcs:disable SlevomatCodingStandard.Files.LineLength.LineTooLong

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20220820143951 extends AbstractMigration
{

    public function up(Schema $schema): void // phpcs:ignore SlevomatCodingStandard.Functions.UnusedParameter.UnusedParameter
    {
        $this->addSql('CREATE TABLE contact (id INT AUTO_INCREMENT NOT NULL, firstname VARCHAR(255) NOT NULL, lastname VARCHAR(255) NOT NULL, phone VARCHAR(255) DEFAULT NULL COMMENT \'(DC2Type:App\\\\Value\\\\PhoneNumber)\', email VARCHAR(255) NOT NULL COMMENT \'(DC2Type:App\\\\Value\\\\EmailAddress)\', notice LONGTEXT DEFAULT NULL, slug VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
    }

    public function down(Schema $schema): void // phpcs:ignore SlevomatCodingStandard.Functions.UnusedParameter.UnusedParameter
    {
        $this->throwIrreversibleMigrationException();
    }

}
