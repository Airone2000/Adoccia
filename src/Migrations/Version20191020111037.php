<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20191020111037 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE category ADD form_id INT DEFAULT NULL, ADD draft_form_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE category ADD CONSTRAINT FK_64C19C15FF69B7D FOREIGN KEY (form_id) REFERENCES form (id)');
        $this->addSql('ALTER TABLE category ADD CONSTRAINT FK_64C19C1A73FE35E FOREIGN KEY (draft_form_id) REFERENCES form (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_64C19C15FF69B7D ON category (form_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_64C19C1A73FE35E ON category (draft_form_id)');
        $this->addSql('ALTER TABLE form DROP FOREIGN KEY FK_5288FD4F12469DE2');
        $this->addSql('DROP INDEX UNIQ_5288FD4F12469DE2 ON form');
        $this->addSql('ALTER TABLE form DROP category_id');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE category DROP FOREIGN KEY FK_64C19C15FF69B7D');
        $this->addSql('ALTER TABLE category DROP FOREIGN KEY FK_64C19C1A73FE35E');
        $this->addSql('DROP INDEX UNIQ_64C19C15FF69B7D ON category');
        $this->addSql('DROP INDEX UNIQ_64C19C1A73FE35E ON category');
        $this->addSql('ALTER TABLE category DROP form_id, DROP draft_form_id');
        $this->addSql('ALTER TABLE form ADD category_id INT NOT NULL');
        $this->addSql('ALTER TABLE form ADD CONSTRAINT FK_5288FD4F12469DE2 FOREIGN KEY (category_id) REFERENCES category (id) ON UPDATE NO ACTION ON DELETE CASCADE');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_5288FD4F12469DE2 ON form (category_id)');
    }
}
