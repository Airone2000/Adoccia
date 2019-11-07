<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20191107163144 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE value DROP FOREIGN KEY FK_1D775834793D36A4');
        $this->addSql('DROP INDEX IDX_1D775834793D36A4 ON value');
        $this->addSql('ALTER TABLE value DROP value_of_type_fichecreator_id');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE value ADD value_of_type_fichecreator_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE value ADD CONSTRAINT FK_1D775834793D36A4 FOREIGN KEY (value_of_type_fichecreator_id) REFERENCES user (id) ON UPDATE NO ACTION ON DELETE SET NULL');
        $this->addSql('CREATE INDEX IDX_1D775834793D36A4 ON value (value_of_type_fichecreator_id)');
    }
}
