<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220311151853 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE group_type_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE group_type (id INT NOT NULL, name VARCHAR(255) NOT NULL, emoji VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('ALTER TABLE "group" ADD type_id INT NOT NULL');
        $this->addSql('ALTER TABLE "group" DROP type');
        $this->addSql('ALTER TABLE "group" ADD CONSTRAINT FK_6DC044C5C54C8C93 FOREIGN KEY (type_id) REFERENCES group_type (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_6DC044C5C54C8C93 ON "group" (type_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE "group" DROP CONSTRAINT FK_6DC044C5C54C8C93');
        $this->addSql('DROP SEQUENCE group_type_id_seq CASCADE');
        $this->addSql('DROP TABLE group_type');
        $this->addSql('DROP INDEX IDX_6DC044C5C54C8C93');
        $this->addSql('ALTER TABLE "group" ADD type VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE "group" DROP type_id');
    }
}
