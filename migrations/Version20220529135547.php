<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220529135547 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE expense_type_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE expense_type (id INT NOT NULL, name VARCHAR(255) NOT NULL, emoji VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('ALTER TABLE expense ADD expense_type_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE expense ADD CONSTRAINT FK_2D3A8DA6A857C7A9 FOREIGN KEY (expense_type_id) REFERENCES expense_type (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_2D3A8DA6A857C7A9 ON expense (expense_type_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE expense DROP CONSTRAINT FK_2D3A8DA6A857C7A9');
        $this->addSql('DROP SEQUENCE expense_type_id_seq CASCADE');
        $this->addSql('DROP TABLE expense_type');
        $this->addSql('DROP INDEX IDX_2D3A8DA6A857C7A9');
        $this->addSql('ALTER TABLE expense DROP expense_type_id');
    }
}
