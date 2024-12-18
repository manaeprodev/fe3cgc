<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241120091550 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE battle (id INT AUTO_INCREMENT NOT NULL, status INT NOT NULL, attackers_success_rate INT NOT NULL, outcome INT DEFAULT NULL, start_date_time DATETIME NOT NULL, end_date_time DATETIME NOT NULL, territory_id_id INT NOT NULL, attacker_faction_id_id INT NOT NULL, INDEX IDX_13991734C58E989F (territory_id_id), INDEX IDX_13991734E09333B9 (attacker_faction_id_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE battle ADD CONSTRAINT FK_13991734C58E989F FOREIGN KEY (territory_id_id) REFERENCES territory (id)');
        $this->addSql('ALTER TABLE battle ADD CONSTRAINT FK_13991734E09333B9 FOREIGN KEY (attacker_faction_id_id) REFERENCES faction (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE battle DROP FOREIGN KEY FK_13991734C58E989F');
        $this->addSql('ALTER TABLE battle DROP FOREIGN KEY FK_13991734E09333B9');
        $this->addSql('DROP TABLE battle');
    }
}
