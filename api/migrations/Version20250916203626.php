<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250916203626 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE core_cms_static_content (id INT AUTO_INCREMENT NOT NULL, code VARCHAR(120) NOT NULL, title VARCHAR(255) DEFAULT NULL, content LONGTEXT DEFAULT NULL, UNIQUE INDEX uniq_core_cms_static_content_code (code), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE faq_entry (id INT AUTO_INCREMENT NOT NULL, question LONGTEXT NOT NULL, answer LONGTEXT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE locale (id INT AUTO_INCREMENT NOT NULL, code VARCHAR(10) NOT NULL, name VARCHAR(255) NOT NULL, auto_translate TINYINT(1) NOT NULL, UNIQUE INDEX UNIQ_4180C69877153098 (code), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE misc_configuration (id INT AUTO_INCREMENT NOT NULL, `key` VARCHAR(255) NOT NULL, value VARCHAR(255) NOT NULL, description VARCHAR(255) DEFAULT NULL, UNIQUE INDEX UNIQ_1FCCAF6A8A90ABA9 (`key`), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE core_cms_static_content');
        $this->addSql('DROP TABLE faq_entry');
        $this->addSql('DROP TABLE locale');
        $this->addSql('DROP TABLE misc_configuration');
    }
}
