<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220527141619 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE balance DROP CONSTRAINT fk_acf41ffef395db7b');
        $this->addSql('DROP INDEX idx_acf41ffef395db7b');
        $this->addSql('ALTER TABLE balance ADD balance_group_id INT NOT NULL');
        $this->addSql('ALTER TABLE balance DROP expense_id');
        $this->addSql('ALTER TABLE balance ADD CONSTRAINT FK_ACF41FFED5D468F7 FOREIGN KEY (balance_group_id) REFERENCES "group" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_ACF41FFED5D468F7 ON balance (balance_group_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE balance DROP CONSTRAINT FK_ACF41FFED5D468F7');
        $this->addSql('DROP INDEX IDX_ACF41FFED5D468F7');
        $this->addSql('ALTER TABLE balance ADD expense_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE balance DROP balance_group_id');
        $this->addSql('ALTER TABLE balance ADD CONSTRAINT fk_acf41ffef395db7b FOREIGN KEY (expense_id) REFERENCES expense (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX idx_acf41ffef395db7b ON balance (expense_id)');
    }
}
