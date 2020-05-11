<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200407074744 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE area DROP FOREIGN KEY FK_D7943D68854679E2');
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D649D60322AC');
        $this->addSql('CREATE TABLE location (id INT AUTO_INCREMENT NOT NULL, organisation_id_id INT NOT NULL, name VARCHAR(255) NOT NULL, code VARCHAR(255) DEFAULT NULL, INDEX IDX_5E9E89CB6C6A4201 (organisation_id_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE location ADD CONSTRAINT FK_5E9E89CB6C6A4201 FOREIGN KEY (organisation_id_id) REFERENCES organisation (id)');
        $this->addSql('DROP TABLE floor');
        $this->addSql('DROP TABLE role');
        $this->addSql('DROP INDEX IDX_D7943D68854679E2 ON area');
        $this->addSql('ALTER TABLE area ADD floor INT DEFAULT NULL, CHANGE floor_id building_id INT NOT NULL');
        $this->addSql('ALTER TABLE area ADD CONSTRAINT FK_D7943D684D2A7E12 FOREIGN KEY (building_id) REFERENCES building (id)');
        $this->addSql('CREATE INDEX IDX_D7943D684D2A7E12 ON area (building_id)');
        $this->addSql('ALTER TABLE building DROP FOREIGN KEY FK_E16F61D49E6B1585');
        $this->addSql('DROP INDEX IDX_E16F61D49E6B1585 ON building');
        $this->addSql('ALTER TABLE building CHANGE organisation_id location_id INT NOT NULL');
        $this->addSql('ALTER TABLE building ADD CONSTRAINT FK_E16F61D464D218E FOREIGN KEY (location_id) REFERENCES location (id)');
        $this->addSql('CREATE INDEX IDX_E16F61D464D218E ON building (location_id)');
        $this->addSql('ALTER TABLE document ADD area_id INT DEFAULT NULL, ADD building_id INT DEFAULT NULL, ADD location_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE document ADD CONSTRAINT FK_D8698A76BD0F409C FOREIGN KEY (area_id) REFERENCES area (id)');
        $this->addSql('ALTER TABLE document ADD CONSTRAINT FK_D8698A764D2A7E12 FOREIGN KEY (building_id) REFERENCES building (id)');
        $this->addSql('ALTER TABLE document ADD CONSTRAINT FK_D8698A7664D218E FOREIGN KEY (location_id) REFERENCES location (id)');
        $this->addSql('CREATE INDEX IDX_D8698A76BD0F409C ON document (area_id)');
        $this->addSql('CREATE INDEX IDX_D8698A764D2A7E12 ON document (building_id)');
        $this->addSql('CREATE INDEX IDX_D8698A7664D218E ON document (location_id)');
        $this->addSql('DROP INDEX IDX_8D93D649D60322AC ON user');
        $this->addSql('ALTER TABLE user ADD role VARCHAR(255) NOT NULL, DROP role_id, DROP roles');
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

        $this->addSql('ALTER TABLE building DROP FOREIGN KEY FK_E16F61D464D218E');
        $this->addSql('ALTER TABLE document DROP FOREIGN KEY FK_D8698A7664D218E');
        $this->addSql('CREATE TABLE floor (id INT AUTO_INCREMENT NOT NULL, building_id INT NOT NULL, name INT NOT NULL, code VARCHAR(10) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, INDEX IDX_BE45D62E4D2A7E12 (building_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE role (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, description VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE floor ADD CONSTRAINT FK_BE45D62E4D2A7E12 FOREIGN KEY (building_id) REFERENCES building (id)');
        $this->addSql('DROP TABLE location');
        $this->addSql('ALTER TABLE area DROP FOREIGN KEY FK_D7943D684D2A7E12');
        $this->addSql('DROP INDEX IDX_D7943D684D2A7E12 ON area');
        $this->addSql('ALTER TABLE area DROP floor, CHANGE building_id floor_id INT NOT NULL');
        $this->addSql('ALTER TABLE area ADD CONSTRAINT FK_D7943D68854679E2 FOREIGN KEY (floor_id) REFERENCES floor (id)');
        $this->addSql('CREATE INDEX IDX_D7943D68854679E2 ON area (floor_id)');
        $this->addSql('DROP INDEX IDX_E16F61D464D218E ON building');
        $this->addSql('ALTER TABLE building CHANGE location_id organisation_id INT NOT NULL');
        $this->addSql('ALTER TABLE building ADD CONSTRAINT FK_E16F61D49E6B1585 FOREIGN KEY (organisation_id) REFERENCES organisation (id)');
        $this->addSql('CREATE INDEX IDX_E16F61D49E6B1585 ON building (organisation_id)');
        $this->addSql('ALTER TABLE document DROP FOREIGN KEY FK_D8698A76BD0F409C');
        $this->addSql('ALTER TABLE document DROP FOREIGN KEY FK_D8698A764D2A7E12');
        $this->addSql('DROP INDEX IDX_D8698A76BD0F409C ON document');
        $this->addSql('DROP INDEX IDX_D8698A764D2A7E12 ON document');
        $this->addSql('DROP INDEX IDX_D8698A7664D218E ON document');
        $this->addSql('ALTER TABLE document DROP area_id, DROP building_id, DROP location_id');
        $this->addSql('ALTER TABLE user ADD role_id INT NOT NULL, ADD roles LONGTEXT CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci` COMMENT \'(DC2Type:json)\', DROP role');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D649D60322AC FOREIGN KEY (role_id) REFERENCES role (id)');
        $this->addSql('CREATE INDEX IDX_8D93D649D60322AC ON user (role_id)');
        $this->addSql('ALTER TABLE view_history DROP FOREIGN KEY FK_EE765B19D86650F');
        $this->addSql('DROP INDEX IDX_EE765B19D86650F ON view_history');
        $this->addSql('ALTER TABLE view_history CHANGE user_id_id user_id INT NOT NULL');
        $this->addSql('ALTER TABLE view_history ADD CONSTRAINT FK_EE765B1A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_EE765B1A76ED395 ON view_history (user_id)');
    }
}
