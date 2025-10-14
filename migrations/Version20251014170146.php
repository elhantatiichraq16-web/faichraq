<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251014170146 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TEMPORARY TABLE __temp__ligne_facture AS SELECT id, facture_id, designation, quantite, prix_unitaire, tva, is_section, position FROM ligne_facture');
        $this->addSql('DROP TABLE ligne_facture');
        $this->addSql('CREATE TABLE ligne_facture (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, facture_id INTEGER NOT NULL, designation VARCHAR(255) NOT NULL, quantite INTEGER NOT NULL, prix_unitaire NUMERIC(10, 2) NOT NULL, tva NUMERIC(5, 2) NOT NULL, is_section BOOLEAN NOT NULL, position INTEGER DEFAULT NULL, remise NUMERIC(5, 2) DEFAULT NULL, CONSTRAINT FK_611F5A297F2DEE08 FOREIGN KEY (facture_id) REFERENCES facture (id) ON UPDATE NO ACTION ON DELETE NO ACTION NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO ligne_facture (id, facture_id, designation, quantite, prix_unitaire, tva, is_section, position) SELECT id, facture_id, designation, quantite, prix_unitaire, tva, is_section, position FROM __temp__ligne_facture');
        $this->addSql('DROP TABLE __temp__ligne_facture');
        $this->addSql('CREATE INDEX IDX_611F5A297F2DEE08 ON ligne_facture (facture_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TEMPORARY TABLE __temp__ligne_facture AS SELECT id, facture_id, designation, quantite, prix_unitaire, tva, is_section, position FROM ligne_facture');
        $this->addSql('DROP TABLE ligne_facture');
        $this->addSql('CREATE TABLE ligne_facture (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, facture_id INTEGER NOT NULL, designation VARCHAR(255) NOT NULL, quantite INTEGER NOT NULL, prix_unitaire NUMERIC(10, 2) NOT NULL, tva NUMERIC(5, 2) NOT NULL, is_section BOOLEAN DEFAULT 0, position INTEGER DEFAULT NULL, CONSTRAINT FK_611F5A297F2DEE08 FOREIGN KEY (facture_id) REFERENCES facture (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO ligne_facture (id, facture_id, designation, quantite, prix_unitaire, tva, is_section, position) SELECT id, facture_id, designation, quantite, prix_unitaire, tva, is_section, position FROM __temp__ligne_facture');
        $this->addSql('DROP TABLE __temp__ligne_facture');
        $this->addSql('CREATE INDEX IDX_611F5A297F2DEE08 ON ligne_facture (facture_id)');
    }
}
