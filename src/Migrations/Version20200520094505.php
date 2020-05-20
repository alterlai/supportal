<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200520094505 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE document_draft DROP FOREIGN KEY FK_2FDDE0E616E5E825');
        $this->addSql('DROP INDEX IDX_2FDDE0E616E5E825 ON document_draft');
        $this->addSql('ALTER TABLE document_draft CHANGE document_id_id document_id INT NOT NULL');
        $this->addSql('ALTER TABLE document_draft ADD CONSTRAINT FK_2FDDE0E6C33F7837 FOREIGN KEY (document_id) REFERENCES document (id)');
        $this->addSql('CREATE INDEX IDX_2FDDE0E6C33F7837 ON document_draft (document_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE document_draft DROP FOREIGN KEY FK_2FDDE0E6C33F7837');
        $this->addSql('DROP INDEX IDX_2FDDE0E6C33F7837 ON document_draft');
        $this->addSql('ALTER TABLE document_draft CHANGE document_id document_id_id INT NOT NULL');
        $this->addSql('ALTER TABLE document_draft ADD CONSTRAINT FK_2FDDE0E616E5E825 FOREIGN KEY (document_id_id) REFERENCES document (id)');
        $this->addSql('CREATE INDEX IDX_2FDDE0E616E5E825 ON document_draft (document_id_id)');
    }
}
