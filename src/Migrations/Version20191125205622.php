<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20191125205622 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE value ADD value_of_type_picture_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE value ADD CONSTRAINT FK_1D7758349404CFE9 FOREIGN KEY (value_of_type_picture_id) REFERENCES picture (id) ON DELETE SET NULL');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_1D7758349404CFE9 ON value (value_of_type_picture_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE value DROP FOREIGN KEY FK_1D7758349404CFE9');
        $this->addSql('DROP INDEX UNIQ_1D7758349404CFE9 ON value');
        $this->addSql('ALTER TABLE value DROP value_of_type_picture_id');
    }
}
