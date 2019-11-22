<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20191122122004 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE fiche ADD picture_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE fiche ADD CONSTRAINT FK_4C13CC78EE45BDBF FOREIGN KEY (picture_id) REFERENCES picture (id) ON DELETE SET NULL');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_4C13CC78EE45BDBF ON fiche (picture_id)');
        $this->addSql('ALTER TABLE picture ADD fiche_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE picture ADD CONSTRAINT FK_16DB4F89DF522508 FOREIGN KEY (fiche_id) REFERENCES fiche (id) ON DELETE SET NULL');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_16DB4F89DF522508 ON picture (fiche_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE fiche DROP FOREIGN KEY FK_4C13CC78EE45BDBF');
        $this->addSql('DROP INDEX UNIQ_4C13CC78EE45BDBF ON fiche');
        $this->addSql('ALTER TABLE fiche DROP picture_id');
        $this->addSql('ALTER TABLE picture DROP FOREIGN KEY FK_16DB4F89DF522508');
        $this->addSql('DROP INDEX UNIQ_16DB4F89DF522508 ON picture');
        $this->addSql('ALTER TABLE picture DROP fiche_id');
    }
}
