<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200911171502 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE content ADD poster_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE content ADD CONSTRAINT FK_FEC530A95BB66C05 FOREIGN KEY (poster_id) REFERENCES image (id)');
        $this->addSql('CREATE INDEX IDX_FEC530A95BB66C05 ON content (poster_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE content DROP FOREIGN KEY FK_FEC530A95BB66C05');
        $this->addSql('DROP INDEX IDX_FEC530A95BB66C05 ON content');
        $this->addSql('ALTER TABLE content DROP poster_id');
    }
}
