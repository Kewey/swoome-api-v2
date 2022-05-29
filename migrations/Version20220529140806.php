<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220529140806 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE expense DROP CONSTRAINT fk_2d3a8da6a857c7a9');
        $this->addSql('DROP INDEX idx_2d3a8da6a857c7a9');
        $this->addSql('ALTER TABLE expense RENAME COLUMN expense_type_id TO type_id');
        $this->addSql('ALTER TABLE expense ADD CONSTRAINT FK_2D3A8DA6C54C8C93 FOREIGN KEY (type_id) REFERENCES expense_type (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_2D3A8DA6C54C8C93 ON expense (type_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE expense DROP CONSTRAINT FK_2D3A8DA6C54C8C93');
        $this->addSql('DROP INDEX IDX_2D3A8DA6C54C8C93');
        $this->addSql('ALTER TABLE expense RENAME COLUMN type_id TO expense_type_id');
        $this->addSql('ALTER TABLE expense ADD CONSTRAINT fk_2d3a8da6a857c7a9 FOREIGN KEY (expense_type_id) REFERENCES expense_type (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX idx_2d3a8da6a857c7a9 ON expense (expense_type_id)');
    }
}
