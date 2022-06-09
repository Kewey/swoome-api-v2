<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220609152030 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE balance (id INT AUTO_INCREMENT NOT NULL, balance_user_id INT DEFAULT NULL, balance_group_id INT NOT NULL, value INT NOT NULL, INDEX IDX_ACF41FFE9385F12F (balance_user_id), INDEX IDX_ACF41FFED5D468F7 (balance_group_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE expense (id INT AUTO_INCREMENT NOT NULL, made_by_id INT NOT NULL, expense_group_id INT NOT NULL, type_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, description LONGTEXT DEFAULT NULL, price INT NOT NULL, created_at DATETIME NOT NULL, expense_at DATETIME NOT NULL, INDEX IDX_2D3A8DA690B9D269 (made_by_id), INDEX IDX_2D3A8DA638351BBE (expense_group_id), INDEX IDX_2D3A8DA6C54C8C93 (type_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE expense_user (expense_id INT NOT NULL, user_id INT NOT NULL, INDEX IDX_3934982BF395DB7B (expense_id), INDEX IDX_3934982BA76ED395 (user_id), PRIMARY KEY(expense_id, user_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE expense_type (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, emoji VARCHAR(255) DEFAULT NULL, is_default TINYINT(1) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE expense_type_group_type (expense_type_id INT NOT NULL, group_type_id INT NOT NULL, INDEX IDX_E235F5EEA857C7A9 (expense_type_id), INDEX IDX_E235F5EE434CD89F (group_type_id), PRIMARY KEY(expense_type_id, group_type_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE expense_type_group (expense_type_id INT NOT NULL, group_id INT NOT NULL, INDEX IDX_D3D8FC3BA857C7A9 (expense_type_id), INDEX IDX_D3D8FC3BFE54D947 (group_id), PRIMARY KEY(expense_type_id, group_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE `group` (id INT AUTO_INCREMENT NOT NULL, type_id INT NOT NULL, name VARCHAR(255) NOT NULL, code VARCHAR(255) NOT NULL, INDEX IDX_6DC044C5C54C8C93 (type_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE group_user (group_id INT NOT NULL, user_id INT NOT NULL, INDEX IDX_A4C98D39FE54D947 (group_id), INDEX IDX_A4C98D39A76ED395 (user_id), PRIMARY KEY(group_id, user_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE group_type (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, emoji VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE media (id INT AUTO_INCREMENT NOT NULL, url VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE refund (id INT AUTO_INCREMENT NOT NULL, refund_group_id INT NOT NULL, refunder_id INT NOT NULL, receiver_id INT NOT NULL, price INT NOT NULL, INDEX IDX_5B2C14588FBBEB44 (refund_group_id), INDEX IDX_5B2C145870C604CC (refunder_id), INDEX IDX_5B2C1458CD53EDB6 (receiver_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE `user` (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, username VARCHAR(255) NOT NULL, is_verified TINYINT(1) NOT NULL, UNIQUE INDEX UNIQ_8D93D649E7927C74 (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE refresh_tokens (id INT AUTO_INCREMENT NOT NULL, refresh_token VARCHAR(128) NOT NULL, username VARCHAR(255) NOT NULL, valid DATETIME NOT NULL, PRIMARY KEY(id))');
        $this->addSql('ALTER TABLE balance ADD CONSTRAINT FK_ACF41FFE9385F12F FOREIGN KEY (balance_user_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE balance ADD CONSTRAINT FK_ACF41FFED5D468F7 FOREIGN KEY (balance_group_id) REFERENCES `group` (id)');
        $this->addSql('ALTER TABLE expense ADD CONSTRAINT FK_2D3A8DA690B9D269 FOREIGN KEY (made_by_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE expense ADD CONSTRAINT FK_2D3A8DA638351BBE FOREIGN KEY (expense_group_id) REFERENCES `group` (id)');
        $this->addSql('ALTER TABLE expense ADD CONSTRAINT FK_2D3A8DA6C54C8C93 FOREIGN KEY (type_id) REFERENCES expense_type (id)');
        $this->addSql('ALTER TABLE expense_user ADD CONSTRAINT FK_3934982BF395DB7B FOREIGN KEY (expense_id) REFERENCES expense (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE expense_user ADD CONSTRAINT FK_3934982BA76ED395 FOREIGN KEY (user_id) REFERENCES `user` (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE expense_type_group_type ADD CONSTRAINT FK_E235F5EEA857C7A9 FOREIGN KEY (expense_type_id) REFERENCES expense_type (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE expense_type_group_type ADD CONSTRAINT FK_E235F5EE434CD89F FOREIGN KEY (group_type_id) REFERENCES group_type (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE expense_type_group ADD CONSTRAINT FK_D3D8FC3BA857C7A9 FOREIGN KEY (expense_type_id) REFERENCES expense_type (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE expense_type_group ADD CONSTRAINT FK_D3D8FC3BFE54D947 FOREIGN KEY (group_id) REFERENCES `group` (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE `group` ADD CONSTRAINT FK_6DC044C5C54C8C93 FOREIGN KEY (type_id) REFERENCES group_type (id)');
        $this->addSql('ALTER TABLE group_user ADD CONSTRAINT FK_A4C98D39FE54D947 FOREIGN KEY (group_id) REFERENCES `group` (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE group_user ADD CONSTRAINT FK_A4C98D39A76ED395 FOREIGN KEY (user_id) REFERENCES `user` (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE refund ADD CONSTRAINT FK_5B2C14588FBBEB44 FOREIGN KEY (refund_group_id) REFERENCES `group` (id)');
        $this->addSql('ALTER TABLE refund ADD CONSTRAINT FK_5B2C145870C604CC FOREIGN KEY (refunder_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE refund ADD CONSTRAINT FK_5B2C1458CD53EDB6 FOREIGN KEY (receiver_id) REFERENCES `user` (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE expense_user DROP FOREIGN KEY FK_3934982BF395DB7B');
        $this->addSql('ALTER TABLE expense DROP FOREIGN KEY FK_2D3A8DA6C54C8C93');
        $this->addSql('ALTER TABLE expense_type_group_type DROP FOREIGN KEY FK_E235F5EEA857C7A9');
        $this->addSql('ALTER TABLE expense_type_group DROP FOREIGN KEY FK_D3D8FC3BA857C7A9');
        $this->addSql('ALTER TABLE balance DROP FOREIGN KEY FK_ACF41FFED5D468F7');
        $this->addSql('ALTER TABLE expense DROP FOREIGN KEY FK_2D3A8DA638351BBE');
        $this->addSql('ALTER TABLE expense_type_group DROP FOREIGN KEY FK_D3D8FC3BFE54D947');
        $this->addSql('ALTER TABLE group_user DROP FOREIGN KEY FK_A4C98D39FE54D947');
        $this->addSql('ALTER TABLE refund DROP FOREIGN KEY FK_5B2C14588FBBEB44');
        $this->addSql('ALTER TABLE expense_type_group_type DROP FOREIGN KEY FK_E235F5EE434CD89F');
        $this->addSql('ALTER TABLE `group` DROP FOREIGN KEY FK_6DC044C5C54C8C93');
        $this->addSql('ALTER TABLE balance DROP FOREIGN KEY FK_ACF41FFE9385F12F');
        $this->addSql('ALTER TABLE expense DROP FOREIGN KEY FK_2D3A8DA690B9D269');
        $this->addSql('ALTER TABLE expense_user DROP FOREIGN KEY FK_3934982BA76ED395');
        $this->addSql('ALTER TABLE group_user DROP FOREIGN KEY FK_A4C98D39A76ED395');
        $this->addSql('ALTER TABLE refund DROP FOREIGN KEY FK_5B2C145870C604CC');
        $this->addSql('ALTER TABLE refund DROP FOREIGN KEY FK_5B2C1458CD53EDB6');
        $this->addSql('DROP TABLE balance');
        $this->addSql('DROP TABLE expense');
        $this->addSql('DROP TABLE expense_user');
        $this->addSql('DROP TABLE expense_type');
        $this->addSql('DROP TABLE expense_type_group_type');
        $this->addSql('DROP TABLE expense_type_group');
        $this->addSql('DROP TABLE `group`');
        $this->addSql('DROP TABLE group_user');
        $this->addSql('DROP TABLE group_type');
        $this->addSql('DROP TABLE media');
        $this->addSql('DROP TABLE refund');
        $this->addSql('DROP TABLE `user`');
        $this->addSql('DROP TABLE refresh_tokens');
    }
}
