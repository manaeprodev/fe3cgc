<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241112103312 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE faction (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(100) NOT NULL, icon VARCHAR(255) NOT NULL, leader_avatar VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE user ADD faction_id INT DEFAULT NULL, CHANGE renown renown INT NOT NULL, CHANGE money money INT NOT NULL, CHANGE first_co first_co TINYINT(1) NOT NULL');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D6494448F8DA FOREIGN KEY (faction_id) REFERENCES faction (id)');
        $this->addSql('CREATE INDEX IDX_8D93D6494448F8DA ON user (faction_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE `user` DROP FOREIGN KEY FK_8D93D6494448F8DA');
        $this->addSql('DROP TABLE faction');
        $this->addSql('DROP INDEX IDX_8D93D6494448F8DA ON `user`');
        $this->addSql('ALTER TABLE `user` DROP faction_id, CHANGE renown renown INT DEFAULT 0 NOT NULL, CHANGE money money INT DEFAULT 1500 NOT NULL, CHANGE first_co first_co TINYINT(1) DEFAULT 1 NOT NULL');
    }
}
