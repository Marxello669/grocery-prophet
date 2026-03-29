<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260328234502 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE price ADD grocery_id INT NOT NULL');
        $this->addSql('ALTER TABLE price ADD CONSTRAINT FK_CAC822D9E96DA631 FOREIGN KEY (grocery_id) REFERENCES grocery (id)');
        $this->addSql('CREATE INDEX IDX_CAC822D9E96DA631 ON price (grocery_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE price DROP FOREIGN KEY FK_CAC822D9E96DA631');
        $this->addSql('DROP INDEX IDX_CAC822D9E96DA631 ON price');
        $this->addSql('ALTER TABLE price DROP grocery_id');
    }
}
