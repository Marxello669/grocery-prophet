<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260420000002 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Change price.value from VARCHAR(255) to DECIMAL(10,2) to fix rounding errors';
    }

    public function up(Schema $schema): void
    {
        // Convert existing string values to decimal
        // Replace commas with dots for decimal format
        $this->addSql('UPDATE price SET value = REPLACE(value, \',\', \'.\') WHERE value IS NOT NULL');
        
        // Change column type to decimal
        $this->addSql('ALTER TABLE price CHANGE COLUMN value value DECIMAL(10, 2) NOT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE price CHANGE COLUMN value value VARCHAR(255) NOT NULL');
    }
}
