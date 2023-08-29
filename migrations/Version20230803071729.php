<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230803071729 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE rdv DROP FOREIGN KEY FK_10C31F8637B987D8');
        $this->addSql('DROP INDEX IDX_10C31F8637B987D8 ON rdv');
        $this->addSql('ALTER TABLE rdv CHANGE created_at created_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', CHANGE status status TINYINT(1) NOT NULL, CHANGE duration_id service_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE rdv ADD CONSTRAINT FK_10C31F86D63673B0 FOREIGN KEY (service_id) REFERENCES service (id)');
        $this->addSql('CREATE INDEX IDX_10C31F86D63673B0 ON rdv (service_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE rdv DROP FOREIGN KEY FK_10C31F86D63673B0');
        $this->addSql('DROP INDEX IDX_10C31F86D63673B0 ON rdv');
        $this->addSql('ALTER TABLE rdv CHANGE created_at created_at DATETIME DEFAULT CURRENT_TIMESTAMP COMMENT \'(DC2Type:datetime_immutable)\', CHANGE status status TINYINT(1) DEFAULT 1, CHANGE service_id duration_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE rdv ADD CONSTRAINT FK_10C31F8637B987D8 FOREIGN KEY (duration_id) REFERENCES service (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE INDEX IDX_10C31F8637B987D8 ON rdv (duration_id)');
    }
}
