<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260419000001 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add BaseProduct entity and relation to Grocery';
    }

    public function up(Schema $schema): void
    {
        // Create base_product table
        $this->addSql('CREATE TABLE base_product (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        
        // Add base_product_id column to grocery table
        $this->addSql('ALTER TABLE grocery ADD base_product_id INT DEFAULT NULL');
        
        // Add foreign key constraint
        $this->addSql('ALTER TABLE grocery ADD CONSTRAINT FK_FD3D6C8DFD61555B FOREIGN KEY (base_product_id) REFERENCES base_product (id)');
        
        // Create index for the foreign key
        $this->addSql('CREATE INDEX IDX_FD3D6C8DFD61555B ON grocery (base_product_id)');
    }

    public function down(Schema $schema): void
    {
        // Drop foreign key constraint
        $this->addSql('ALTER TABLE grocery DROP FOREIGN KEY FK_FD3D6C8DFD61555B');
        
        // Drop index
        $this->addSql('DROP INDEX IDX_FD3D6C8DFD61555B ON grocery');
        
        // Remove base_product_id column from grocery table
        $this->addSql('ALTER TABLE grocery DROP COLUMN base_product_id');
        
        // Drop base_product table
        $this->addSql('DROP TABLE base_product');
    }
}
