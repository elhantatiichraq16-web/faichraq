<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20251015120000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create meme table';
    }

    public function up(Schema $schema): void
    {
        // SQLite compat SQL; adjust for Postgres if needed via Doctrine platform.
        $this->addSql('CREATE TABLE meme (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, title VARCHAR(255) NOT NULL, image_url VARCHAR(255) NOT NULL, caption CLOB DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE meme');
    }
}
