<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200316104734 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE actions (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(10) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE area (id INT AUTO_INCREMENT NOT NULL, floor_id_id INT NOT NULL, name VARCHAR(255) NOT NULL, code VARCHAR(10) NOT NULL, INDEX IDX_D7943D68AD65E761 (floor_id_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE building (id INT AUTO_INCREMENT NOT NULL, organisation_id_id INT NOT NULL, name VARCHAR(255) NOT NULL, code VARCHAR(10) NOT NULL, INDEX IDX_E16F61D46C6A4201 (organisation_id_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE discipline (id INT AUTO_INCREMENT NOT NULL, code DOUBLE PRECISION NOT NULL, description VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE document (id INT AUTO_INCREMENT NOT NULL, discipline_code_id INT NOT NULL, file_name VARCHAR(100) NOT NULL, updated_at DATETIME NOT NULL, description VARCHAR(255) DEFAULT NULL, INDEX IDX_D8698A764D129E4B (discipline_code_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE document_history (id INT AUTO_INCREMENT NOT NULL, updated_by_id INT NOT NULL, updated_at DATETIME NOT NULL, revision_description VARCHAR(255) DEFAULT NULL, revision INT NOT NULL, INDEX IDX_83D5D697896DBBDE (updated_by_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE floor (id INT AUTO_INCREMENT NOT NULL, building_id_id INT NOT NULL, name INT NOT NULL, code VARCHAR(10) NOT NULL, INDEX IDX_BE45D62E13E42FCD (building_id_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE organisation (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, logo VARCHAR(255) DEFAULT NULL, color VARCHAR(7) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE role (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, description VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE view_history (id INT AUTO_INCREMENT NOT NULL, user_id_id INT NOT NULL, action_type_id INT NOT NULL, document_id_id INT NOT NULL, timestamp DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_EE765B19D86650F (user_id_id), INDEX IDX_EE765B11FEE0472 (action_type_id), INDEX IDX_EE765B116E5E825 (document_id_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE area ADD CONSTRAINT FK_D7943D68AD65E761 FOREIGN KEY (floor_id_id) REFERENCES floor (id)');
        $this->addSql('ALTER TABLE building ADD CONSTRAINT FK_E16F61D46C6A4201 FOREIGN KEY (organisation_id_id) REFERENCES organisation (id)');
        $this->addSql('ALTER TABLE document ADD CONSTRAINT FK_D8698A764D129E4B FOREIGN KEY (discipline_code_id) REFERENCES discipline (id)');
        $this->addSql('ALTER TABLE document_history ADD CONSTRAINT FK_83D5D697896DBBDE FOREIGN KEY (updated_by_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE floor ADD CONSTRAINT FK_BE45D62E13E42FCD FOREIGN KEY (building_id_id) REFERENCES building (id)');
        $this->addSql('ALTER TABLE view_history ADD CONSTRAINT FK_EE765B19D86650F FOREIGN KEY (user_id_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE view_history ADD CONSTRAINT FK_EE765B11FEE0472 FOREIGN KEY (action_type_id) REFERENCES actions (id)');
        $this->addSql('ALTER TABLE view_history ADD CONSTRAINT FK_EE765B116E5E825 FOREIGN KEY (document_id_id) REFERENCES document (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE view_history DROP FOREIGN KEY FK_EE765B11FEE0472');
        $this->addSql('ALTER TABLE floor DROP FOREIGN KEY FK_BE45D62E13E42FCD');
        $this->addSql('ALTER TABLE document DROP FOREIGN KEY FK_D8698A764D129E4B');
        $this->addSql('ALTER TABLE view_history DROP FOREIGN KEY FK_EE765B116E5E825');
        $this->addSql('ALTER TABLE area DROP FOREIGN KEY FK_D7943D68AD65E761');
        $this->addSql('ALTER TABLE building DROP FOREIGN KEY FK_E16F61D46C6A4201');
        $this->addSql('DROP TABLE actions');
        $this->addSql('DROP TABLE area');
        $this->addSql('DROP TABLE building');
        $this->addSql('DROP TABLE discipline');
        $this->addSql('DROP TABLE document');
        $this->addSql('DROP TABLE document_history');
        $this->addSql('DROP TABLE floor');
        $this->addSql('DROP TABLE organisation');
        $this->addSql('DROP TABLE role');
        $this->addSql('DROP TABLE view_history');
    }
}
