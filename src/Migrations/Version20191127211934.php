<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20191127211934 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE category_search ADD user_id INT DEFAULT NULL, DROP user, CHANGE guest_unique_id guest_unique_id VARCHAR(80) DEFAULT NULL, CHANGE title title VARCHAR(255) DEFAULT NULL, CHANGE order_by order_by VARCHAR(50) DEFAULT NULL, CHANGE filter filter VARCHAR(50) DEFAULT NULL');
        $this->addSql('ALTER TABLE category_search ADD CONSTRAINT FK_BB1811DCA76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('CREATE INDEX IDX_BB1811DCA76ED395 ON category_search (user_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE category_search DROP FOREIGN KEY FK_BB1811DCA76ED395');
        $this->addSql('DROP INDEX IDX_BB1811DCA76ED395 ON category_search');
        $this->addSql('ALTER TABLE category_search ADD user VARCHAR(255) NOT NULL COLLATE utf8mb4_unicode_ci, DROP user_id, CHANGE guest_unique_id guest_unique_id VARCHAR(255) NOT NULL COLLATE utf8mb4_unicode_ci, CHANGE title title VARCHAR(255) NOT NULL COLLATE utf8mb4_unicode_ci, CHANGE order_by order_by VARCHAR(255) NOT NULL COLLATE utf8mb4_unicode_ci, CHANGE filter filter VARCHAR(255) NOT NULL COLLATE utf8mb4_unicode_ci');
    }
}
