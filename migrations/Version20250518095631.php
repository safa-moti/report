<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250518095631 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TEMPORARY TABLE __temp__book AS SELECT id FROM book
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE book
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE book (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, title VARCHAR(255) NOT NULL, isbn VARCHAR(255) NOT NULL, author VARCHAR(255) NOT NULL)
        SQL);
        $this->addSql(<<<'SQL'
            INSERT INTO book (id) SELECT id FROM __temp__book
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE __temp__book
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TEMPORARY TABLE __temp__book AS SELECT id FROM book
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE book
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE book (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, string VARCHAR(255) NOT NULL, int VARCHAR(255) NOT NULL)
        SQL);
        $this->addSql(<<<'SQL'
            INSERT INTO book (id) SELECT id FROM __temp__book
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE __temp__book
        SQL);
    }
}
