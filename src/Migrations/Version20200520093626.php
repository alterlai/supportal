<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200520093626 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE document_draft (id INT AUTO_INCREMENT NOT NULL, document_id_id INT NOT NULL, uploaded_by_id INT NOT NULL, uploaded_at DATETIME NOT NULL, rejection_description LONGTEXT DEFAULT NULL, rejected_at DATETIME DEFAULT NULL, INDEX IDX_2FDDE0E616E5E825 (document_id_id), INDEX IDX_2FDDE0E6A2B28FE8 (uploaded_by_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE issues (id INT AUTO_INCREMENT NOT NULL, closed TINYINT(1) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE document_draft ADD CONSTRAINT FK_2FDDE0E616E5E825 FOREIGN KEY (document_id_id) REFERENCES document (id)');
        $this->addSql('ALTER TABLE document_draft ADD CONSTRAINT FK_2FDDE0E6A2B28FE8 FOREIGN KEY (uploaded_by_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE area ADD CONSTRAINT FK_D7943D684D2A7E12 FOREIGN KEY (building_id) REFERENCES building (id)');
        $this->addSql('ALTER TABLE building ADD CONSTRAINT FK_E16F61D464D218E FOREIGN KEY (location_id) REFERENCES location (id)');
        $this->addSql('ALTER TABLE document ADD CONSTRAINT FK_D8698A76A5522701 FOREIGN KEY (discipline_id) REFERENCES discipline (id)');
        $this->addSql('ALTER TABLE document ADD CONSTRAINT FK_D8698A76BD0F409C FOREIGN KEY (area_id) REFERENCES area (id)');
        $this->addSql('ALTER TABLE document ADD CONSTRAINT FK_D8698A764D2A7E12 FOREIGN KEY (building_id) REFERENCES building (id)');
        $this->addSql('ALTER TABLE document ADD CONSTRAINT FK_D8698A7664D218E FOREIGN KEY (location_id) REFERENCES location (id)');
        $this->addSql('ALTER TABLE document ADD CONSTRAINT FK_D8698A7661232A4F FOREIGN KEY (document_type_id) REFERENCES document_type (id)');
        $this->addSql('ALTER TABLE document_history ADD CONSTRAINT FK_83D5D697896DBBDE FOREIGN KEY (updated_by_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE document_history ADD CONSTRAINT FK_83D5D697C33F7837 FOREIGN KEY (document_id) REFERENCES document (id)');
        $this->addSql('ALTER TABLE location ADD CONSTRAINT FK_5E9E89CB9E6B1585 FOREIGN KEY (organisation_id) REFERENCES organisation (id)');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D6499E6B1585 FOREIGN KEY (organisation_id) REFERENCES organisation (id)');
        $this->addSql('ALTER TABLE view_history ADD CONSTRAINT FK_EE765B19D86650F FOREIGN KEY (user_id_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE view_history ADD CONSTRAINT FK_EE765B11FEE0472 FOREIGN KEY (action_type_id) REFERENCES actions (id)');
        $this->addSql('ALTER TABLE view_history ADD CONSTRAINT FK_EE765B1C33F7837 FOREIGN KEY (document_id) REFERENCES document (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE document_draft');
        $this->addSql('DROP TABLE issues');
        $this->addSql('ALTER TABLE area DROP FOREIGN KEY FK_D7943D684D2A7E12');
        $this->addSql('ALTER TABLE building DROP FOREIGN KEY FK_E16F61D464D218E');
        $this->addSql('ALTER TABLE document DROP FOREIGN KEY FK_D8698A76A5522701');
        $this->addSql('ALTER TABLE document DROP FOREIGN KEY FK_D8698A76BD0F409C');
        $this->addSql('ALTER TABLE document DROP FOREIGN KEY FK_D8698A764D2A7E12');
        $this->addSql('ALTER TABLE document DROP FOREIGN KEY FK_D8698A7664D218E');
        $this->addSql('ALTER TABLE document DROP FOREIGN KEY FK_D8698A7661232A4F');
        $this->addSql('ALTER TABLE document_history DROP FOREIGN KEY FK_83D5D697896DBBDE');
        $this->addSql('ALTER TABLE document_history DROP FOREIGN KEY FK_83D5D697C33F7837');
        $this->addSql('ALTER TABLE location DROP FOREIGN KEY FK_5E9E89CB9E6B1585');
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D6499E6B1585');
        $this->addSql('ALTER TABLE view_history DROP FOREIGN KEY FK_EE765B19D86650F');
        $this->addSql('ALTER TABLE view_history DROP FOREIGN KEY FK_EE765B11FEE0472');
        $this->addSql('ALTER TABLE view_history DROP FOREIGN KEY FK_EE765B1C33F7837');
    }
}
