<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251011120653 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE facture (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, client_id INTEGER NOT NULL, reference VARCHAR(50) NOT NULL, date_facture DATETIME NOT NULL, date_echeance DATETIME DEFAULT NULL, etat VARCHAR(20) NOT NULL, total_ht NUMERIC(10, 2) NOT NULL, total_ttc NUMERIC(10, 2) NOT NULL, devise VARCHAR(3) NOT NULL, notes CLOB DEFAULT NULL, CONSTRAINT FK_FE86641019EB6921 FOREIGN KEY (client_id) REFERENCES tiers (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_FE866410AEA34913 ON facture (reference)');
        $this->addSql('CREATE INDEX IDX_FE86641019EB6921 ON facture (client_id)');
        $this->addSql('CREATE TABLE ligne_facture (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, facture_id INTEGER NOT NULL, designation VARCHAR(255) NOT NULL, quantite INTEGER NOT NULL, prix_unitaire NUMERIC(10, 2) NOT NULL, tva NUMERIC(5, 2) NOT NULL, CONSTRAINT FK_611F5A297F2DEE08 FOREIGN KEY (facture_id) REFERENCES facture (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE INDEX IDX_611F5A297F2DEE08 ON ligne_facture (facture_id)');
        $this->addSql('CREATE TABLE tiers (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, nom VARCHAR(255) NOT NULL, email VARCHAR(255) DEFAULT NULL, adresse CLOB DEFAULT NULL, telephone VARCHAR(20) DEFAULT NULL, num_tva VARCHAR(50) DEFAULT NULL)');
        $this->addSql('CREATE TABLE user (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, email VARCHAR(180) NOT NULL, roles CLOB NOT NULL --(DC2Type:json)
        , password VARCHAR(255) NOT NULL, nom VARCHAR(255) NOT NULL)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D649E7927C74 ON user (email)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE facture');
        $this->addSql('DROP TABLE ligne_facture');
        $this->addSql('DROP TABLE tiers');
        $this->addSql('DROP TABLE user');
    }
}
