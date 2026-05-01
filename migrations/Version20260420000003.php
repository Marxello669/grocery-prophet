<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260420000003 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add UNIQUE constraints to prevent duplicate entries';
    }

    public function up(Schema $schema): void
    {
        // Add unique constraint on BaseProduct.name
        $this->addSql('ALTER TABLE base_product ADD CONSTRAINT UNIQ_71F6C5E35E237E06 UNIQUE (name)');

        // Add composite unique constraint on Grocery (name, base_product_id)
        $this->addSql('ALTER TABLE grocery ADD CONSTRAINT UNIQ_7E146D9E5E237E06B3E0ABFE UNIQUE (name, base_product_id)');

        // Add unique constraint on ShoppingListItem.grocery_id (one item per list)
        $this->addSql('ALTER TABLE shopping_list_item ADD CONSTRAINT UNIQ_70A74581F02F56DF UNIQUE (grocery_id)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE base_product DROP INDEX UNIQ_71F6C5E35E237E06');
        $this->addSql('ALTER TABLE grocery DROP INDEX UNIQ_7E146D9E5E237E06B3E0ABFE');
        $this->addSql('ALTER TABLE shopping_list_item DROP INDEX UNIQ_70A74581F02F56DF');
    }
}
