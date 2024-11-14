<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241113160134 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE territory ADD faction_id INT NOT NULL');
        $this->addSql('ALTER TABLE territory ADD CONSTRAINT FK_E97439664448F8DA FOREIGN KEY (faction_id) REFERENCES faction (id)');
        $this->addSql('CREATE INDEX IDX_E97439664448F8DA ON territory (faction_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE territory DROP FOREIGN KEY FK_E97439664448F8DA');
        $this->addSql('DROP INDEX IDX_E97439664448F8DA ON territory');
        $this->addSql('ALTER TABLE territory DROP faction_id');
    }
}
