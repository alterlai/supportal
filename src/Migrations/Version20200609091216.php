<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200609091216 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE view_history DROP FOREIGN KEY FK_EE765B11FEE0472');
        $this->addSql('CREATE TABLE user_action (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, document_id INT NOT NULL, file_type VARCHAR(5) NOT NULL, downloaded_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', deadline DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', returned_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_229E97AFA76ED395 (user_id), INDEX IDX_229E97AFC33F7837 (document_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE user_action ADD CONSTRAINT FK_229E97AFA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE user_action ADD CONSTRAINT FK_229E97AFC33F7837 FOREIGN KEY (document_id) REFERENCES document (id)');
        $this->addSql('DROP TABLE actions');
        $this->addSql('DROP TABLE view_history');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE actions (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(10) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, document_type VARCHAR(5) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE view_history (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, action_type_id INT NOT NULL, document_id INT NOT NULL, timestamp DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_EE765B1A76ED395 (user_id), INDEX IDX_EE765B11FEE0472 (action_type_id), INDEX IDX_EE765B1C33F7837 (document_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE view_history ADD CONSTRAINT FK_EE765B11FEE0472 FOREIGN KEY (action_type_id) REFERENCES actions (id)');
        $this->addSql('ALTER TABLE view_history ADD CONSTRAINT FK_EE765B1A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE view_history ADD CONSTRAINT FK_EE765B1C33F7837 FOREIGN KEY (document_id) REFERENCES document (id)');
        $this->addSql('DROP TABLE user_action');
    }
}
