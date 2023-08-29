<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230828084438 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE note DROP FOREIGN KEY FK_CFBDFA144CCE3F86');
        $this->addSql('DROP INDEX UNIQ_CFBDFA144CCE3F86 ON note');
        $this->addSql('ALTER TABLE note DROP rdv_id');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE note ADD rdv_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE note ADD CONSTRAINT FK_CFBDFA144CCE3F86 FOREIGN KEY (rdv_id) REFERENCES rdv (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_CFBDFA144CCE3F86 ON note (rdv_id)');
    }
}
