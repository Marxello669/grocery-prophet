<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260419000002 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create recipe and recipe_ingredient tables';
    }

    public function up(Schema $schema): void
    {
        // Create recipe table
        $this->addSql('CREATE TABLE recipe (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, description LONGTEXT DEFAULT NULL, servings INT DEFAULT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        
        // Create recipe_ingredient table
        $this->addSql('CREATE TABLE recipe_ingredient (id INT AUTO_INCREMENT NOT NULL, recipe_id INT NOT NULL, base_product_id INT NOT NULL, quantity DOUBLE PRECISION NOT NULL, unit VARCHAR(255) NOT NULL, INDEX IDX_5EC4AF59A0FBEE (recipe_id), INDEX IDX_5EC4AF599FD1B4 (base_product_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        
        // Add foreign key constraints
        $this->addSql('ALTER TABLE recipe_ingredient ADD CONSTRAINT FK_5EC4AF59A0FBEE FOREIGN KEY (recipe_id) REFERENCES recipe (id)');
        $this->addSql('ALTER TABLE recipe_ingredient ADD CONSTRAINT FK_5EC4AF599FD1B4 FOREIGN KEY (base_product_id) REFERENCES base_product (id)');
    }

    public function down(Schema $schema): void
    {
        // Drop foreign key constraints
        $this->addSql('ALTER TABLE recipe_ingredient DROP FOREIGN KEY FK_5EC4AF59A0FBEE');
        $this->addSql('ALTER TABLE recipe_ingredient DROP FOREIGN KEY FK_5EC4AF599FD1B4');
        
        // Drop tables
        $this->addSql('DROP TABLE recipe_ingredient');
        $this->addSql('DROP TABLE recipe');
    }
}
