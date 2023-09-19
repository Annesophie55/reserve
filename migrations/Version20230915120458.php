<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230915120458 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE message DROP FOREIGN KEY FK_B6BD307FE92F8F78');
        $this->addSql('ALTER TABLE message DROP FOREIGN KEY FK_B6BD307FF624B39D');
        $this->addSql('DROP INDEX IDX_B6BD307FE92F8F78 ON message');
        $this->addSql('DROP INDEX IDX_B6BD307FF624B39D ON message');
        $this->addSql('ALTER TABLE message ADD recipient_id_id INT NOT NULL, ADD title VARCHAR(255) NOT NULL, DROP sender_id, CHANGE recipient_id sender_id_id INT NOT NULL');
        $this->addSql('ALTER TABLE message ADD CONSTRAINT FK_B6BD307F6061F7CF FOREIGN KEY (sender_id_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE message ADD CONSTRAINT FK_B6BD307F2B6945EC FOREIGN KEY (recipient_id_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_B6BD307F6061F7CF ON message (sender_id_id)');
        $this->addSql('CREATE INDEX IDX_B6BD307F2B6945EC ON message (recipient_id_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE message DROP FOREIGN KEY FK_B6BD307F6061F7CF');
        $this->addSql('ALTER TABLE message DROP FOREIGN KEY FK_B6BD307F2B6945EC');
        $this->addSql('DROP INDEX IDX_B6BD307F6061F7CF ON message');
        $this->addSql('DROP INDEX IDX_B6BD307F2B6945EC ON message');
        $this->addSql('ALTER TABLE message ADD sender_id INT DEFAULT NULL, ADD recipient_id INT NOT NULL, DROP sender_id_id, DROP recipient_id_id, DROP title');
        $this->addSql('ALTER TABLE message ADD CONSTRAINT FK_B6BD307FE92F8F78 FOREIGN KEY (recipient_id) REFERENCES user (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('ALTER TABLE message ADD CONSTRAINT FK_B6BD307FF624B39D FOREIGN KEY (sender_id) REFERENCES user (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE INDEX IDX_B6BD307FE92F8F78 ON message (recipient_id)');
        $this->addSql('CREATE INDEX IDX_B6BD307FF624B39D ON message (sender_id)');
    }
}
