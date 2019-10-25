<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20191025070315 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE form_area ADD margin_top SMALLINT DEFAULT NULL, ADD margin_bottom SMALLINT DEFAULT NULL, ADD margin_left SMALLINT DEFAULT NULL, ADD margin_right SMALLINT DEFAULT NULL, ADD border_top_width SMALLINT DEFAULT NULL, ADD border_top_color VARCHAR(15) DEFAULT NULL, ADD border_bottom_width SMALLINT DEFAULT NULL, ADD border_bottom_color VARCHAR(15) DEFAULT NULL, ADD border_left_width SMALLINT DEFAULT NULL, ADD border_left_color VARCHAR(15) DEFAULT NULL, ADD border_right_width SMALLINT DEFAULT NULL, ADD border_right_color VARCHAR(15) DEFAULT NULL, ADD background_color VARCHAR(15) DEFAULT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE form_area DROP margin_top, DROP margin_bottom, DROP margin_left, DROP margin_right, DROP border_top_width, DROP border_top_color, DROP border_bottom_width, DROP border_bottom_color, DROP border_left_width, DROP border_left_color, DROP border_right_width, DROP border_right_color, DROP background_color');
    }
}
