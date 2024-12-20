<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20241220172602 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'misc';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE discipline CHANGE na�me name VARCHAR(255) NOT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE discipline CHANGE name na�me VARCHAR(255) NOT NULL');
    }
}
