<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220811095302 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE refund DROP FOREIGN KEY FK_5B2C145870C604CC');
        $this->addSql('ALTER TABLE refund ADD CONSTRAINT FK_5B2C145870C604CC FOREIGN KEY (refunder_id) REFERENCES `user` (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE refund DROP FOREIGN KEY FK_5B2C145870C604CC');
        $this->addSql('ALTER TABLE refund ADD CONSTRAINT FK_5B2C145870C604CC FOREIGN KEY (refunder_id) REFERENCES user (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
    }
}
