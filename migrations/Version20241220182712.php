<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20241220182712 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Fogotten tutorial relation';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE tutorial_step ADD tutorial_id INT NOT NULL');
        $this->addSql('ALTER TABLE tutorial_step ADD CONSTRAINT FK_3C7F797189366B7B FOREIGN KEY (tutorial_id) REFERENCES tutorial (id)');
        $this->addSql('CREATE INDEX IDX_3C7F797189366B7B ON tutorial_step (tutorial_id)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE tutorial_step DROP FOREIGN KEY FK_3C7F797189366B7B');
        $this->addSql('DROP INDEX IDX_3C7F797189366B7B ON tutorial_step');
        $this->addSql('ALTER TABLE tutorial_step DROP tutorial_id');
    }
}
