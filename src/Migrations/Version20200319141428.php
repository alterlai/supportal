<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200319141428 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE user ADD role VARCHAR(255) NOT NULL, DROP roles');
        $this->addSql('ALTER TABLE view_history DROP FOREIGN KEY FK_EE765B1A76ED395');
        $this->addSql('DROP INDEX IDX_EE765B1A76ED395 ON view_history');
        $this->addSql('ALTER TABLE view_history CHANGE user_id user_id_id INT NOT NULL');
        $this->addSql('ALTER TABLE view_history ADD CONSTRAINT FK_EE765B19D86650F FOREIGN KEY (user_id_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_EE765B19D86650F ON view_history (user_id_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE user ADD roles LONGTEXT CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci` COMMENT \'(DC2Type:json)\', DROP role');
        $this->addSql('ALTER TABLE view_history DROP FOREIGN KEY FK_EE765B19D86650F');
        $this->addSql('DROP INDEX IDX_EE765B19D86650F ON view_history');
        $this->addSql('ALTER TABLE view_history CHANGE user_id_id user_id INT NOT NULL');
        $this->addSql('ALTER TABLE view_history ADD CONSTRAINT FK_EE765B1A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_EE765B1A76ED395 ON view_history (user_id)');
    }
}
