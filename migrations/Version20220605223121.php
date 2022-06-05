<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220605223121 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE media DROP CONSTRAINT fk_6a2ca10c86d8b6f4');
        $this->addSql('DROP INDEX uniq_6a2ca10c86d8b6f4');
        $this->addSql('ALTER TABLE media DROP user_avatar_id');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE media ADD user_avatar_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE media ADD CONSTRAINT fk_6a2ca10c86d8b6f4 FOREIGN KEY (user_avatar_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE UNIQUE INDEX uniq_6a2ca10c86d8b6f4 ON media (user_avatar_id)');
    }
}
