<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200211120725 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE activity (id INT AUTO_INCREMENT NOT NULL, nom LONGTEXT NOT NULL, duration INT NOT NULL, duration_param VARCHAR(255) NOT NULL, price DOUBLE PRECISION DEFAULT NULL, description LONGTEXT NOT NULL, lat DOUBLE PRECISION DEFAULT NULL, longitude DOUBLE PRECISION DEFAULT NULL, address LONGTEXT DEFAULT NULL, user_id INT NOT NULL, votes_up INT DEFAULT NULL, votes_down INT DEFAULT NULL, state INT DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE hotel (id INT AUTO_INCREMENT NOT NULL, nom LONGTEXT NOT NULL, duration INT NOT NULL, duration_param VARCHAR(255) NOT NULL, price DOUBLE PRECISION DEFAULT NULL, description LONGTEXT NOT NULL, lat DOUBLE PRECISION DEFAULT NULL, longitude DOUBLE PRECISION DEFAULT NULL, address LONGTEXT DEFAULT NULL, user_id INT NOT NULL, votes_up INT DEFAULT NULL, votes_down INT DEFAULT NULL, state INT DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE members (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, project_id INT NOT NULL, rank INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE project (id INT AUTO_INCREMENT NOT NULL, nom LONGTEXT NOT NULL, created_time VARCHAR(255) NOT NULL, description LONGTEXT NOT NULL, owner_id INT NOT NULL, begin_date VARCHAR(255) DEFAULT NULL, end_date VARCHAR(255) DEFAULT NULL, invite VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE users ADD premium TINYINT(1) NOT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE activity');
        $this->addSql('DROP TABLE hotel');
        $this->addSql('DROP TABLE members');
        $this->addSql('DROP TABLE project');
        $this->addSql('ALTER TABLE users DROP premium');
    }
}
