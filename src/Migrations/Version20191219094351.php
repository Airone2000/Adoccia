<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20191219094351 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE image_crop (id INT AUTO_INCREMENT NOT NULL, image_id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', width VARCHAR(255) NOT NULL, height VARCHAR(255) NOT NULL, x VARCHAR(255) NOT NULL, y VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_1FF8D7053DA5256D (image_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE image (id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', crop_id INT DEFAULT NULL, filename VARCHAR(50) NOT NULL, UNIQUE INDEX UNIQ_C53D045F3C0BE965 (filename), UNIQUE INDEX UNIQ_C53D045F888579EE (crop_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE image_crop ADD CONSTRAINT FK_1FF8D7053DA5256D FOREIGN KEY (image_id) REFERENCES image (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE image ADD CONSTRAINT FK_C53D045F888579EE FOREIGN KEY (crop_id) REFERENCES image_crop (id) ON DELETE SET NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE image DROP FOREIGN KEY FK_C53D045F888579EE');
        $this->addSql('ALTER TABLE image_crop DROP FOREIGN KEY FK_1FF8D7053DA5256D');
        $this->addSql('DROP TABLE image_crop');
        $this->addSql('DROP TABLE image');
    }
}
