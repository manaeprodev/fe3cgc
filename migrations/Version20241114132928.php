<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241114132928 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE user ADD title_id INT DEFAULT NULL, DROP title');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D649A9F87BD FOREIGN KEY (title_id) REFERENCES title (id)');
        $this->addSql('CREATE INDEX IDX_8D93D649A9F87BD ON user (title_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE `user` DROP FOREIGN KEY FK_8D93D649A9F87BD');
        $this->addSql('DROP INDEX IDX_8D93D649A9F87BD ON `user`');
        $this->addSql('ALTER TABLE `user` ADD title VARCHAR(255) DEFAULT NULL, DROP title_id');
    }
}
