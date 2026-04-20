<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260419235336 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE grocery RENAME INDEX idx_fd3d6c8dfd61555b TO IDX_DDAA6289D63EB556');
        $this->addSql('ALTER TABLE recipe ADD link VARCHAR(255) DEFAULT NULL, CHANGE servings servings INT NOT NULL');
        $this->addSql('ALTER TABLE recipe_ingredient RENAME INDEX idx_5ec4af59a0fbee TO IDX_22D1FE1359D8A214');
        $this->addSql('ALTER TABLE recipe_ingredient RENAME INDEX idx_5ec4af599fd1b4 TO IDX_22D1FE13D63EB556');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE grocery RENAME INDEX idx_ddaa6289d63eb556 TO IDX_FD3D6C8DFD61555B');
        $this->addSql('ALTER TABLE recipe DROP link, CHANGE servings servings INT DEFAULT NULL');
        $this->addSql('ALTER TABLE recipe_ingredient RENAME INDEX idx_22d1fe1359d8a214 TO IDX_5EC4AF59A0FBEE');
        $this->addSql('ALTER TABLE recipe_ingredient RENAME INDEX idx_22d1fe13d63eb556 TO IDX_5EC4AF599FD1B4');
    }
}
