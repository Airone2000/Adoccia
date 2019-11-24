<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20191124132456 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE value DROP FOREIGN KEY FK_1D7758349404CFE9');
        $this->addSql('DROP TABLE picture');
        $this->addSql('DROP INDEX UNIQ_1D7758349404CFE9 ON value');
        $this->addSql('ALTER TABLE value DROP value_of_type_picture_id');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE picture (id INT AUTO_INCREMENT NOT NULL, category_id INT DEFAULT NULL, fiche_id INT DEFAULT NULL, user_id INT DEFAULT NULL, filename VARCHAR(255) NOT NULL COLLATE utf8mb4_unicode_ci, public SMALLINT NOT NULL, updated_at DATETIME DEFAULT NULL, UNIQUE INDEX UNIQ_16DB4F8912469DE2 (category_id), UNIQUE INDEX UNIQ_16DB4F89DF522508 (fiche_id), INDEX IDX_16DB4F89A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE picture ADD CONSTRAINT FK_16DB4F8912469DE2 FOREIGN KEY (category_id) REFERENCES category (id) ON UPDATE NO ACTION ON DELETE SET NULL');
        $this->addSql('ALTER TABLE picture ADD CONSTRAINT FK_16DB4F89A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON UPDATE NO ACTION ON DELETE SET NULL');
        $this->addSql('ALTER TABLE picture ADD CONSTRAINT FK_16DB4F89DF522508 FOREIGN KEY (fiche_id) REFERENCES fiche (id) ON UPDATE NO ACTION ON DELETE SET NULL');
        $this->addSql('ALTER TABLE value ADD value_of_type_picture_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE value ADD CONSTRAINT FK_1D7758349404CFE9 FOREIGN KEY (value_of_type_picture_id) REFERENCES picture (id) ON UPDATE NO ACTION ON DELETE SET NULL');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_1D7758349404CFE9 ON value (value_of_type_picture_id)');
    }
}
