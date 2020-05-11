<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200316145541 extends AbstractMigration
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
        $this->addSql('DROP TABLE floor');
        $this->addSql('DROP INDEX IDX_D7943D68854679E2 ON area');
        $this->addSql('ALTER TABLE area ADD floor INT DEFAULT NULL, CHANGE floor_id building_id INT NOT NULL');
        $this->addSql('ALTER TABLE area ADD CONSTRAINT FK_D7943D684D2A7E12 FOREIGN KEY (building_id) REFERENCES building (id)');
        $this->addSql('CREATE INDEX IDX_D7943D684D2A7E12 ON area (building_id)');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D649D60322AC FOREIGN KEY (role_id) REFERENCES role (id)');
        $this->addSql('CREATE INDEX IDX_8D93D649D60322AC ON user (role_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE floor (id INT AUTO_INCREMENT NOT NULL, building_id INT NOT NULL, name INT NOT NULL, code VARCHAR(10) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, INDEX IDX_BE45D62E4D2A7E12 (building_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE floor ADD CONSTRAINT FK_BE45D62E4D2A7E12 FOREIGN KEY (building_id) REFERENCES building (id)');
        $this->addSql('ALTER TABLE area DROP FOREIGN KEY FK_D7943D684D2A7E12');
        $this->addSql('DROP INDEX IDX_D7943D684D2A7E12 ON area');
        $this->addSql('ALTER TABLE area DROP floor, CHANGE building_id floor_id INT NOT NULL');
        $this->addSql('ALTER TABLE area ADD CONSTRAINT FK_D7943D68854679E2 FOREIGN KEY (floor_id) REFERENCES floor (id)');
        $this->addSql('CREATE INDEX IDX_D7943D68854679E2 ON area (floor_id)');
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D649D60322AC');
        $this->addSql('DROP INDEX IDX_8D93D649D60322AC ON user');
    }
}
