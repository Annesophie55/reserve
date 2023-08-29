<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230801140946 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE rdv CHANGE status status TINYINT(1) NOT NULL, CHANGE duration duration_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE rdv ADD CONSTRAINT FK_10C31F8637B987D8 FOREIGN KEY (duration_id) REFERENCES service (id)');
        $this->addSql('CREATE INDEX IDX_10C31F8637B987D8 ON rdv (duration_id)');
        $this->addSql('ALTER TABLE service ADD duration INT DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE rdv DROP FOREIGN KEY FK_10C31F8637B987D8');
        $this->addSql('DROP INDEX IDX_10C31F8637B987D8 ON rdv');
        $this->addSql('ALTER TABLE rdv CHANGE status status TINYINT(1) DEFAULT 1, CHANGE duration_id duration INT DEFAULT NULL');
        $this->addSql('ALTER TABLE service DROP duration');
    }
}
