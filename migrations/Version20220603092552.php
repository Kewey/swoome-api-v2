<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220603092552 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE expense_type_group_type (expense_type_id INT NOT NULL, group_type_id INT NOT NULL, PRIMARY KEY(expense_type_id, group_type_id))');
        $this->addSql('CREATE INDEX IDX_E235F5EEA857C7A9 ON expense_type_group_type (expense_type_id)');
        $this->addSql('CREATE INDEX IDX_E235F5EE434CD89F ON expense_type_group_type (group_type_id)');
        $this->addSql('CREATE TABLE expense_type_group (expense_type_id INT NOT NULL, group_id INT NOT NULL, PRIMARY KEY(expense_type_id, group_id))');
        $this->addSql('CREATE INDEX IDX_D3D8FC3BA857C7A9 ON expense_type_group (expense_type_id)');
        $this->addSql('CREATE INDEX IDX_D3D8FC3BFE54D947 ON expense_type_group (group_id)');
        $this->addSql('ALTER TABLE expense_type_group_type ADD CONSTRAINT FK_E235F5EEA857C7A9 FOREIGN KEY (expense_type_id) REFERENCES expense_type (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE expense_type_group_type ADD CONSTRAINT FK_E235F5EE434CD89F FOREIGN KEY (group_type_id) REFERENCES group_type (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE expense_type_group ADD CONSTRAINT FK_D3D8FC3BA857C7A9 FOREIGN KEY (expense_type_id) REFERENCES expense_type (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE expense_type_group ADD CONSTRAINT FK_D3D8FC3BFE54D947 FOREIGN KEY (group_id) REFERENCES "group" (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE expense_type ADD is_default BOOLEAN NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP TABLE expense_type_group_type');
        $this->addSql('DROP TABLE expense_type_group');
        $this->addSql('ALTER TABLE expense_type DROP is_default');
    }
}
