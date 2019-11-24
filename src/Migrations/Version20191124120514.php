<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20191124120514 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE category DROP FOREIGN KEY FK_64C19C1EE45BDBF');
        $this->addSql('DROP INDEX UNIQ_64C19C1EE45BDBF ON category');
        $this->addSql('ALTER TABLE category DROP picture_id');
        $this->addSql('ALTER TABLE fiche DROP FOREIGN KEY FK_4C13CC78EE45BDBF');
        $this->addSql('DROP INDEX UNIQ_4C13CC78EE45BDBF ON fiche');
        $this->addSql('ALTER TABLE fiche DROP picture_id');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE category ADD picture_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE category ADD CONSTRAINT FK_64C19C1EE45BDBF FOREIGN KEY (picture_id) REFERENCES picture (id) ON UPDATE NO ACTION ON DELETE SET NULL');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_64C19C1EE45BDBF ON category (picture_id)');
        $this->addSql('ALTER TABLE fiche ADD picture_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE fiche ADD CONSTRAINT FK_4C13CC78EE45BDBF FOREIGN KEY (picture_id) REFERENCES picture (id) ON UPDATE NO ACTION ON DELETE SET NULL');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_4C13CC78EE45BDBF ON fiche (picture_id)');
    }
}
