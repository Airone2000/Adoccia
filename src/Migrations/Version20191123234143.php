<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20191123234143 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE category (id INT AUTO_INCREMENT NOT NULL, created_by_id INT DEFAULT NULL, picture_id INT DEFAULT NULL, form_id INT DEFAULT NULL, draft_form_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, description LONGTEXT DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, online TINYINT(1) DEFAULT \'0\' NOT NULL, public TINYINT(1) DEFAULT \'0\' NOT NULL, INDEX IDX_64C19C1B03A8386 (created_by_id), UNIQUE INDEX UNIQ_64C19C1EE45BDBF (picture_id), UNIQUE INDEX UNIQ_64C19C15FF69B7D (form_id), UNIQUE INDEX UNIQ_64C19C1A73FE35E (draft_form_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE password_reset (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, cached_email VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL, expires_at DATETIME NOT NULL, password_changed_at DATETIME DEFAULT NULL, token VARCHAR(70) NOT NULL, UNIQUE INDEX UNIQ_B10172525F37A13B (token), INDEX IDX_B1017252A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, username VARCHAR(180) NOT NULL, roles JSON NOT NULL, email VARCHAR(255) NOT NULL, password VARCHAR(255) NOT NULL, permissions JSON NOT NULL, is_super_admin TINYINT(1) DEFAULT \'0\' NOT NULL, UNIQUE INDEX UNIQ_8D93D649F85E0677 (username), UNIQUE INDEX UNIQ_8D93D649E7927C74 (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE search (id INT AUTO_INCREMENT NOT NULL, category_id INT DEFAULT NULL, user_id INT DEFAULT NULL, criterias JSON NOT NULL, created_at DATETIME NOT NULL, INDEX IDX_B4F0DBA712469DE2 (category_id), INDEX IDX_B4F0DBA7A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE value (id INT AUTO_INCREMENT NOT NULL, fiche_id INT NOT NULL, widget_id INT NOT NULL, value_of_type_picture_id INT DEFAULT NULL, widget_immutable_id VARCHAR(50) NOT NULL, value_of_type_text LONGTEXT DEFAULT NULL, value_of_type_string LONGTEXT DEFAULT NULL, value_of_type_int VARCHAR(100) DEFAULT NULL, value_of_type_float VARCHAR(100) DEFAULT NULL, value_of_type_date DATE DEFAULT NULL, value_of_type_time TIME DEFAULT NULL, value_of_type_radio LONGTEXT DEFAULT NULL, value_of_type_email VARCHAR(255) DEFAULT NULL, value_of_type_map JSON DEFAULT NULL, value_of_type_video LONGTEXT DEFAULT NULL, value_of_type_button JSON DEFAULT NULL, INDEX IDX_1D775834DF522508 (fiche_id), INDEX IDX_1D775834FBE885E2 (widget_id), UNIQUE INDEX UNIQ_1D7758349404CFE9 (value_of_type_picture_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE form (id INT AUTO_INCREMENT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE fiche (id INT AUTO_INCREMENT NOT NULL, picture_id INT DEFAULT NULL, category_id INT NOT NULL, creator_id INT DEFAULT NULL, title VARCHAR(255) NOT NULL, published TINYINT(1) DEFAULT \'0\' NOT NULL, valid TINYINT(1) DEFAULT \'1\' NOT NULL, UNIQUE INDEX UNIQ_4C13CC78EE45BDBF (picture_id), INDEX IDX_4C13CC7812469DE2 (category_id), INDEX IDX_4C13CC7861220EA6 (creator_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE widget (id INT AUTO_INCREMENT NOT NULL, form_area_id INT NOT NULL, immutable_id VARCHAR(50) NOT NULL, type VARCHAR(30) NOT NULL, inner_text LONGTEXT DEFAULT NULL, min_length BIGINT DEFAULT NULL, max_length BIGINT DEFAULT NULL, required TINYINT(1) DEFAULT \'0\' NOT NULL, text_align VARCHAR(20) DEFAULT NULL, text_color VARCHAR(15) DEFAULT NULL, input_placeholder VARCHAR(255) DEFAULT NULL, min VARCHAR(100) DEFAULT NULL, max VARCHAR(100) DEFAULT NULL, decimal_count VARCHAR(100) DEFAULT NULL, date_format VARCHAR(20) DEFAULT NULL, time_format VARCHAR(20) DEFAULT NULL, choices JSON DEFAULT NULL, multiple_values TINYINT(1) DEFAULT \'0\' NOT NULL, min_markers INT DEFAULT NULL, max_markers INT DEFAULT NULL, UNIQUE INDEX UNIQ_85F91ED08C6320A7 (form_area_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE picture (id INT AUTO_INCREMENT NOT NULL, category_id INT DEFAULT NULL, fiche_id INT DEFAULT NULL, user_id INT DEFAULT NULL, filename VARCHAR(255) NOT NULL, public SMALLINT NOT NULL, updated_at DATETIME DEFAULT NULL, UNIQUE INDEX UNIQ_16DB4F8912469DE2 (category_id), UNIQUE INDEX UNIQ_16DB4F89DF522508 (fiche_id), INDEX IDX_16DB4F89A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE form_area (id INT AUTO_INCREMENT NOT NULL, form_id INT NOT NULL, widget_id INT DEFAULT NULL, width DOUBLE PRECISION DEFAULT \'100\' NOT NULL, position INT NOT NULL, margin_top SMALLINT DEFAULT NULL, margin_bottom SMALLINT DEFAULT NULL, margin_left SMALLINT DEFAULT NULL, margin_right SMALLINT DEFAULT NULL, padding_top SMALLINT DEFAULT NULL, padding_bottom SMALLINT DEFAULT NULL, padding_left SMALLINT DEFAULT NULL, padding_right SMALLINT DEFAULT NULL, border_top_width SMALLINT DEFAULT NULL, border_top_color VARCHAR(15) DEFAULT NULL, border_bottom_width SMALLINT DEFAULT NULL, border_bottom_color VARCHAR(15) DEFAULT NULL, border_left_width SMALLINT DEFAULT NULL, border_left_color VARCHAR(15) DEFAULT NULL, border_right_width SMALLINT DEFAULT NULL, border_right_color VARCHAR(15) DEFAULT NULL, background_color VARCHAR(15) DEFAULT NULL, widget_vertical_alignment VARCHAR(15) DEFAULT NULL, INDEX IDX_43B5397F5FF69B7D (form_id), UNIQUE INDEX UNIQ_43B5397FFBE885E2 (widget_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE category ADD CONSTRAINT FK_64C19C1B03A8386 FOREIGN KEY (created_by_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE category ADD CONSTRAINT FK_64C19C1EE45BDBF FOREIGN KEY (picture_id) REFERENCES picture (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE category ADD CONSTRAINT FK_64C19C15FF69B7D FOREIGN KEY (form_id) REFERENCES form (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE category ADD CONSTRAINT FK_64C19C1A73FE35E FOREIGN KEY (draft_form_id) REFERENCES form (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE password_reset ADD CONSTRAINT FK_B1017252A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE search ADD CONSTRAINT FK_B4F0DBA712469DE2 FOREIGN KEY (category_id) REFERENCES category (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE search ADD CONSTRAINT FK_B4F0DBA7A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE value ADD CONSTRAINT FK_1D775834DF522508 FOREIGN KEY (fiche_id) REFERENCES fiche (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE value ADD CONSTRAINT FK_1D775834FBE885E2 FOREIGN KEY (widget_id) REFERENCES widget (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE value ADD CONSTRAINT FK_1D7758349404CFE9 FOREIGN KEY (value_of_type_picture_id) REFERENCES picture (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE fiche ADD CONSTRAINT FK_4C13CC78EE45BDBF FOREIGN KEY (picture_id) REFERENCES picture (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE fiche ADD CONSTRAINT FK_4C13CC7812469DE2 FOREIGN KEY (category_id) REFERENCES category (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE fiche ADD CONSTRAINT FK_4C13CC7861220EA6 FOREIGN KEY (creator_id) REFERENCES user (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE widget ADD CONSTRAINT FK_85F91ED08C6320A7 FOREIGN KEY (form_area_id) REFERENCES form_area (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE picture ADD CONSTRAINT FK_16DB4F8912469DE2 FOREIGN KEY (category_id) REFERENCES category (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE picture ADD CONSTRAINT FK_16DB4F89DF522508 FOREIGN KEY (fiche_id) REFERENCES fiche (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE picture ADD CONSTRAINT FK_16DB4F89A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE form_area ADD CONSTRAINT FK_43B5397F5FF69B7D FOREIGN KEY (form_id) REFERENCES form (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE form_area ADD CONSTRAINT FK_43B5397FFBE885E2 FOREIGN KEY (widget_id) REFERENCES widget (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE search DROP FOREIGN KEY FK_B4F0DBA712469DE2');
        $this->addSql('ALTER TABLE fiche DROP FOREIGN KEY FK_4C13CC7812469DE2');
        $this->addSql('ALTER TABLE picture DROP FOREIGN KEY FK_16DB4F8912469DE2');
        $this->addSql('ALTER TABLE category DROP FOREIGN KEY FK_64C19C1B03A8386');
        $this->addSql('ALTER TABLE password_reset DROP FOREIGN KEY FK_B1017252A76ED395');
        $this->addSql('ALTER TABLE search DROP FOREIGN KEY FK_B4F0DBA7A76ED395');
        $this->addSql('ALTER TABLE fiche DROP FOREIGN KEY FK_4C13CC7861220EA6');
        $this->addSql('ALTER TABLE picture DROP FOREIGN KEY FK_16DB4F89A76ED395');
        $this->addSql('ALTER TABLE category DROP FOREIGN KEY FK_64C19C15FF69B7D');
        $this->addSql('ALTER TABLE category DROP FOREIGN KEY FK_64C19C1A73FE35E');
        $this->addSql('ALTER TABLE form_area DROP FOREIGN KEY FK_43B5397F5FF69B7D');
        $this->addSql('ALTER TABLE value DROP FOREIGN KEY FK_1D775834DF522508');
        $this->addSql('ALTER TABLE picture DROP FOREIGN KEY FK_16DB4F89DF522508');
        $this->addSql('ALTER TABLE value DROP FOREIGN KEY FK_1D775834FBE885E2');
        $this->addSql('ALTER TABLE form_area DROP FOREIGN KEY FK_43B5397FFBE885E2');
        $this->addSql('ALTER TABLE category DROP FOREIGN KEY FK_64C19C1EE45BDBF');
        $this->addSql('ALTER TABLE value DROP FOREIGN KEY FK_1D7758349404CFE9');
        $this->addSql('ALTER TABLE fiche DROP FOREIGN KEY FK_4C13CC78EE45BDBF');
        $this->addSql('ALTER TABLE widget DROP FOREIGN KEY FK_85F91ED08C6320A7');
        $this->addSql('DROP TABLE category');
        $this->addSql('DROP TABLE password_reset');
        $this->addSql('DROP TABLE user');
        $this->addSql('DROP TABLE search');
        $this->addSql('DROP TABLE value');
        $this->addSql('DROP TABLE form');
        $this->addSql('DROP TABLE fiche');
        $this->addSql('DROP TABLE widget');
        $this->addSql('DROP TABLE picture');
        $this->addSql('DROP TABLE form_area');
    }
}
