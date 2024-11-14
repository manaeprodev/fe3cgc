<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241113155639 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE territory (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(100) NOT NULL, money_prize INT DEFAULT NULL, renown_prize INT DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE territories_neighbors (territory_id INT NOT NULL, neighboring_territory_id INT NOT NULL, INDEX IDX_5154A6F73F74AD4 (territory_id), INDEX IDX_5154A6FEBEBFE6C (neighboring_territory_id), PRIMARY KEY(territory_id, neighboring_territory_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE territories_neighbors ADD CONSTRAINT FK_5154A6F73F74AD4 FOREIGN KEY (territory_id) REFERENCES territory (id)');
        $this->addSql('ALTER TABLE territories_neighbors ADD CONSTRAINT FK_5154A6FEBEBFE6C FOREIGN KEY (neighboring_territory_id) REFERENCES territory (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE territories_neighbors DROP FOREIGN KEY FK_5154A6F73F74AD4');
        $this->addSql('ALTER TABLE territories_neighbors DROP FOREIGN KEY FK_5154A6FEBEBFE6C');
        $this->addSql('DROP TABLE territory');
        $this->addSql('DROP TABLE territories_neighbors');
    }
}
