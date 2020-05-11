<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200316110244 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE area DROP FOREIGN KEY FK_D7943D68AD65E761');
        $this->addSql('DROP INDEX IDX_D7943D68AD65E761 ON area');
        $this->addSql('ALTER TABLE area CHANGE floor_id_id floor_id INT NOT NULL');
        $this->addSql('ALTER TABLE area ADD CONSTRAINT FK_D7943D68854679E2 FOREIGN KEY (floor_id) REFERENCES floor (id)');
        $this->addSql('CREATE INDEX IDX_D7943D68854679E2 ON area (floor_id)');
        $this->addSql('ALTER TABLE building DROP FOREIGN KEY FK_E16F61D46C6A4201');
        $this->addSql('DROP INDEX IDX_E16F61D46C6A4201 ON building');
        $this->addSql('ALTER TABLE building CHANGE organisation_id_id organisation_id INT NOT NULL');
        $this->addSql('ALTER TABLE building ADD CONSTRAINT FK_E16F61D49E6B1585 FOREIGN KEY (organisation_id) REFERENCES organisation (id)');
        $this->addSql('CREATE INDEX IDX_E16F61D49E6B1585 ON building (organisation_id)');
        $this->addSql('ALTER TABLE document DROP FOREIGN KEY FK_D8698A764D129E4B');
        $this->addSql('DROP INDEX IDX_D8698A764D129E4B ON document');
        $this->addSql('ALTER TABLE document CHANGE discipline_code_id discipline_id INT NOT NULL');
        $this->addSql('ALTER TABLE document ADD CONSTRAINT FK_D8698A76A5522701 FOREIGN KEY (discipline_id) REFERENCES discipline (id)');
        $this->addSql('CREATE INDEX IDX_D8698A76A5522701 ON document (discipline_id)');
        $this->addSql('ALTER TABLE floor DROP FOREIGN KEY FK_BE45D62E13E42FCD');
        $this->addSql('DROP INDEX IDX_BE45D62E13E42FCD ON floor');
        $this->addSql('ALTER TABLE floor CHANGE building_id_id building_id INT NOT NULL');
        $this->addSql('ALTER TABLE floor ADD CONSTRAINT FK_BE45D62E4D2A7E12 FOREIGN KEY (building_id) REFERENCES building (id)');
        $this->addSql('CREATE INDEX IDX_BE45D62E4D2A7E12 ON floor (building_id)');
        $this->addSql('ALTER TABLE view_history DROP FOREIGN KEY FK_EE765B116E5E825');
        $this->addSql('ALTER TABLE view_history DROP FOREIGN KEY FK_EE765B19D86650F');
        $this->addSql('DROP INDEX IDX_EE765B116E5E825 ON view_history');
        $this->addSql('DROP INDEX IDX_EE765B19D86650F ON view_history');
        $this->addSql('ALTER TABLE view_history ADD user_id INT NOT NULL, ADD document_id INT NOT NULL, DROP user_id_id, DROP document_id_id');
        $this->addSql('ALTER TABLE view_history ADD CONSTRAINT FK_EE765B1A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE view_history ADD CONSTRAINT FK_EE765B1C33F7837 FOREIGN KEY (document_id) REFERENCES document (id)');
        $this->addSql('CREATE INDEX IDX_EE765B1A76ED395 ON view_history (user_id)');
        $this->addSql('CREATE INDEX IDX_EE765B1C33F7837 ON view_history (document_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE area DROP FOREIGN KEY FK_D7943D68854679E2');
        $this->addSql('DROP INDEX IDX_D7943D68854679E2 ON area');
        $this->addSql('ALTER TABLE area CHANGE floor_id floor_id_id INT NOT NULL');
        $this->addSql('ALTER TABLE area ADD CONSTRAINT FK_D7943D68AD65E761 FOREIGN KEY (floor_id_id) REFERENCES floor (id)');
        $this->addSql('CREATE INDEX IDX_D7943D68AD65E761 ON area (floor_id_id)');
        $this->addSql('ALTER TABLE building DROP FOREIGN KEY FK_E16F61D49E6B1585');
        $this->addSql('DROP INDEX IDX_E16F61D49E6B1585 ON building');
        $this->addSql('ALTER TABLE building CHANGE organisation_id organisation_id_id INT NOT NULL');
        $this->addSql('ALTER TABLE building ADD CONSTRAINT FK_E16F61D46C6A4201 FOREIGN KEY (organisation_id_id) REFERENCES organisation (id)');
        $this->addSql('CREATE INDEX IDX_E16F61D46C6A4201 ON building (organisation_id_id)');
        $this->addSql('ALTER TABLE document DROP FOREIGN KEY FK_D8698A76A5522701');
        $this->addSql('DROP INDEX IDX_D8698A76A5522701 ON document');
        $this->addSql('ALTER TABLE document CHANGE discipline_id discipline_code_id INT NOT NULL');
        $this->addSql('ALTER TABLE document ADD CONSTRAINT FK_D8698A764D129E4B FOREIGN KEY (discipline_code_id) REFERENCES discipline (id)');
        $this->addSql('CREATE INDEX IDX_D8698A764D129E4B ON document (discipline_code_id)');
        $this->addSql('ALTER TABLE floor DROP FOREIGN KEY FK_BE45D62E4D2A7E12');
        $this->addSql('DROP INDEX IDX_BE45D62E4D2A7E12 ON floor');
        $this->addSql('ALTER TABLE floor CHANGE building_id building_id_id INT NOT NULL');
        $this->addSql('ALTER TABLE floor ADD CONSTRAINT FK_BE45D62E13E42FCD FOREIGN KEY (building_id_id) REFERENCES building (id)');
        $this->addSql('CREATE INDEX IDX_BE45D62E13E42FCD ON floor (building_id_id)');
        $this->addSql('ALTER TABLE view_history DROP FOREIGN KEY FK_EE765B1A76ED395');
        $this->addSql('ALTER TABLE view_history DROP FOREIGN KEY FK_EE765B1C33F7837');
        $this->addSql('DROP INDEX IDX_EE765B1A76ED395 ON view_history');
        $this->addSql('DROP INDEX IDX_EE765B1C33F7837 ON view_history');
        $this->addSql('ALTER TABLE view_history ADD user_id_id INT NOT NULL, ADD document_id_id INT NOT NULL, DROP user_id, DROP document_id');
        $this->addSql('ALTER TABLE view_history ADD CONSTRAINT FK_EE765B116E5E825 FOREIGN KEY (document_id_id) REFERENCES document (id)');
        $this->addSql('ALTER TABLE view_history ADD CONSTRAINT FK_EE765B19D86650F FOREIGN KEY (user_id_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_EE765B116E5E825 ON view_history (document_id_id)');
        $this->addSql('CREATE INDEX IDX_EE765B19D86650F ON view_history (user_id_id)');
    }
}
