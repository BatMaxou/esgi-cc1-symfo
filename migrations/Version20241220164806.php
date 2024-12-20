<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20241220164806 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Initilal schema';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE comment (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, parent_id INT DEFAULT NULL, content LONGTEXT NOT NULL, discr VARCHAR(255) NOT NULL, INDEX IDX_9474526CA76ED395 (user_id), INDEX IDX_9474526C727ACA70 (parent_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE discipline (id INT AUTO_INCREMENT NOT NULL, na�me VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE discipline_subscription (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, discipline_id INT NOT NULL, INDEX IDX_65ADF5CFA76ED395 (user_id), INDEX IDX_65ADF5CFA5522701 (discipline_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE rating (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, tutorial_id INT NOT NULL, rate INT NOT NULL, INDEX IDX_D8892622A76ED395 (user_id), INDEX IDX_D889262289366B7B (tutorial_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE tutorial (id INT AUTO_INCREMENT NOT NULL, author_id INT NOT NULL, title VARCHAR(255) NOT NULL, description LONGTEXT NOT NULL, difficulty INT DEFAULT NULL, duration INT DEFAULT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_C66BFFE9F675F31B (author_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE tutorial_discipline (tutorial_id INT NOT NULL, discipline_id INT NOT NULL, INDEX IDX_C93505B889366B7B (tutorial_id), INDEX IDX_C93505B8A5522701 (discipline_id), PRIMARY KEY(tutorial_id, discipline_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE tutorial_comment (id INT NOT NULL, tutorial_id INT NOT NULL, INDEX IDX_EAF3258989366B7B (tutorial_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE tutorial_step (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(255) NOT NULL, content LONGTEXT NOT NULL, position INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE tutorial_step_comment (id INT NOT NULL, step_id INT NOT NULL, INDEX IDX_67935D7473B21E9C (step_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE comment ADD CONSTRAINT FK_9474526CA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE comment ADD CONSTRAINT FK_9474526C727ACA70 FOREIGN KEY (parent_id) REFERENCES comment (id)');
        $this->addSql('ALTER TABLE discipline_subscription ADD CONSTRAINT FK_65ADF5CFA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE discipline_subscription ADD CONSTRAINT FK_65ADF5CFA5522701 FOREIGN KEY (discipline_id) REFERENCES discipline (id)');
        $this->addSql('ALTER TABLE rating ADD CONSTRAINT FK_D8892622A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE rating ADD CONSTRAINT FK_D889262289366B7B FOREIGN KEY (tutorial_id) REFERENCES tutorial (id)');
        $this->addSql('ALTER TABLE tutorial ADD CONSTRAINT FK_C66BFFE9F675F31B FOREIGN KEY (author_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE tutorial_discipline ADD CONSTRAINT FK_C93505B889366B7B FOREIGN KEY (tutorial_id) REFERENCES tutorial (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE tutorial_discipline ADD CONSTRAINT FK_C93505B8A5522701 FOREIGN KEY (discipline_id) REFERENCES discipline (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE tutorial_comment ADD CONSTRAINT FK_EAF3258989366B7B FOREIGN KEY (tutorial_id) REFERENCES tutorial (id)');
        $this->addSql('ALTER TABLE tutorial_comment ADD CONSTRAINT FK_EAF32589BF396750 FOREIGN KEY (id) REFERENCES comment (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE tutorial_step_comment ADD CONSTRAINT FK_67935D7473B21E9C FOREIGN KEY (step_id) REFERENCES tutorial_step (id)');
        $this->addSql('ALTER TABLE tutorial_step_comment ADD CONSTRAINT FK_67935D74BF396750 FOREIGN KEY (id) REFERENCES comment (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE comment DROP FOREIGN KEY FK_9474526CA76ED395');
        $this->addSql('ALTER TABLE comment DROP FOREIGN KEY FK_9474526C727ACA70');
        $this->addSql('ALTER TABLE discipline_subscription DROP FOREIGN KEY FK_65ADF5CFA76ED395');
        $this->addSql('ALTER TABLE discipline_subscription DROP FOREIGN KEY FK_65ADF5CFA5522701');
        $this->addSql('ALTER TABLE rating DROP FOREIGN KEY FK_D8892622A76ED395');
        $this->addSql('ALTER TABLE rating DROP FOREIGN KEY FK_D889262289366B7B');
        $this->addSql('ALTER TABLE tutorial DROP FOREIGN KEY FK_C66BFFE9F675F31B');
        $this->addSql('ALTER TABLE tutorial_discipline DROP FOREIGN KEY FK_C93505B889366B7B');
        $this->addSql('ALTER TABLE tutorial_discipline DROP FOREIGN KEY FK_C93505B8A5522701');
        $this->addSql('ALTER TABLE tutorial_comment DROP FOREIGN KEY FK_EAF3258989366B7B');
        $this->addSql('ALTER TABLE tutorial_comment DROP FOREIGN KEY FK_EAF32589BF396750');
        $this->addSql('ALTER TABLE tutorial_step_comment DROP FOREIGN KEY FK_67935D7473B21E9C');
        $this->addSql('ALTER TABLE tutorial_step_comment DROP FOREIGN KEY FK_67935D74BF396750');
        $this->addSql('DROP TABLE comment');
        $this->addSql('DROP TABLE discipline');
        $this->addSql('DROP TABLE discipline_subscription');
        $this->addSql('DROP TABLE rating');
        $this->addSql('DROP TABLE tutorial');
        $this->addSql('DROP TABLE tutorial_discipline');
        $this->addSql('DROP TABLE tutorial_comment');
        $this->addSql('DROP TABLE tutorial_step');
        $this->addSql('DROP TABLE tutorial_step_comment');
    }
}
