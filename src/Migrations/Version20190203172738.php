<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190203172738 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        $this->addSql('CREATE TEMPORARY TABLE __temp__gurubeer_order AS SELECT id, name, file_name, created, updated FROM gurubeer_order');
        $this->addSql('DROP TABLE gurubeer_order');
        $this->addSql('CREATE TABLE gurubeer_order (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, creator_id INTEGER DEFAULT NULL, name VARCHAR(255) NOT NULL COLLATE BINARY, file_name VARCHAR(255) DEFAULT NULL COLLATE BINARY, created DATETIME NOT NULL, updated DATETIME NOT NULL, CONSTRAINT FK_36BDD3BF61220EA6 FOREIGN KEY (creator_id) REFERENCES user (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO gurubeer_order (id, name, file_name, created, updated) SELECT id, name, file_name, created, updated FROM __temp__gurubeer_order');
        $this->addSql('DROP TABLE __temp__gurubeer_order');
        $this->addSql('CREATE INDEX IDX_36BDD3BF61220EA6 ON gurubeer_order (creator_id)');
        $this->addSql('DROP INDEX IDX_87D5A3E38D9F6D38');
        $this->addSql('CREATE TEMPORARY TABLE __temp__gurubeer_item AS SELECT id, order_id, name, state, type, quantity, quantity_updated FROM gurubeer_item');
        $this->addSql('DROP TABLE gurubeer_item');
        $this->addSql('CREATE TABLE gurubeer_item (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, order_id INTEGER DEFAULT NULL, name VARCHAR(255) NOT NULL COLLATE BINARY, state VARCHAR(255) NOT NULL COLLATE BINARY, type VARCHAR(255) NOT NULL COLLATE BINARY, quantity INTEGER NOT NULL, quantity_updated INTEGER NOT NULL, CONSTRAINT FK_87D5A3E38D9F6D38 FOREIGN KEY (order_id) REFERENCES gurubeer_order_version (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO gurubeer_item (id, order_id, name, state, type, quantity, quantity_updated) SELECT id, order_id, name, state, type, quantity, quantity_updated FROM __temp__gurubeer_item');
        $this->addSql('DROP TABLE __temp__gurubeer_item');
        $this->addSql('CREATE INDEX IDX_87D5A3E38D9F6D38 ON gurubeer_item (order_id)');
        $this->addSql('DROP INDEX IDX_D69CA2E08D9F6D38');
        $this->addSql('DROP INDEX IDX_D69CA2E061220EA6');
        $this->addSql('CREATE TEMPORARY TABLE __temp__gurubeer_order_version AS SELECT id, order_id, creator_id, state, version FROM gurubeer_order_version');
        $this->addSql('DROP TABLE gurubeer_order_version');
        $this->addSql('CREATE TABLE gurubeer_order_version (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, order_id INTEGER DEFAULT NULL, creator_id INTEGER DEFAULT NULL, state VARCHAR(255) NOT NULL COLLATE BINARY, version INTEGER NOT NULL, CONSTRAINT FK_D69CA2E08D9F6D38 FOREIGN KEY (order_id) REFERENCES gurubeer_order (id) NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_D69CA2E061220EA6 FOREIGN KEY (creator_id) REFERENCES user (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO gurubeer_order_version (id, order_id, creator_id, state, version) SELECT id, order_id, creator_id, state, version FROM __temp__gurubeer_order_version');
        $this->addSql('DROP TABLE __temp__gurubeer_order_version');
        $this->addSql('CREATE INDEX IDX_D69CA2E08D9F6D38 ON gurubeer_order_version (order_id)');
        $this->addSql('CREATE INDEX IDX_D69CA2E061220EA6 ON gurubeer_order_version (creator_id)');
    }

    public function down(Schema $schema) : void
    {
        $this->addSql('DROP INDEX IDX_87D5A3E38D9F6D38');
        $this->addSql('CREATE TEMPORARY TABLE __temp__gurubeer_item AS SELECT id, order_id, name, state, type, quantity, quantity_updated FROM gurubeer_item');
        $this->addSql('DROP TABLE gurubeer_item');
        $this->addSql('CREATE TABLE gurubeer_item (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, order_id INTEGER DEFAULT NULL, name VARCHAR(255) NOT NULL, state VARCHAR(255) NOT NULL, type VARCHAR(255) NOT NULL, quantity INTEGER NOT NULL, quantity_updated INTEGER NOT NULL)');
        $this->addSql('INSERT INTO gurubeer_item (id, order_id, name, state, type, quantity, quantity_updated) SELECT id, order_id, name, state, type, quantity, quantity_updated FROM __temp__gurubeer_item');
        $this->addSql('DROP TABLE __temp__gurubeer_item');
        $this->addSql('CREATE INDEX IDX_87D5A3E38D9F6D38 ON gurubeer_item (order_id)');
        $this->addSql('DROP INDEX IDX_36BDD3BF61220EA6');
        $this->addSql('CREATE TEMPORARY TABLE __temp__gurubeer_order AS SELECT id, name, file_name, created, updated FROM gurubeer_order');
        $this->addSql('DROP TABLE gurubeer_order');
        $this->addSql('CREATE TABLE gurubeer_order (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, name VARCHAR(255) NOT NULL, file_name VARCHAR(255) DEFAULT NULL, created DATETIME NOT NULL, updated DATETIME NOT NULL)');
        $this->addSql('INSERT INTO gurubeer_order (id, name, file_name, created, updated) SELECT id, name, file_name, created, updated FROM __temp__gurubeer_order');
        $this->addSql('DROP TABLE __temp__gurubeer_order');
        $this->addSql('DROP INDEX IDX_D69CA2E08D9F6D38');
        $this->addSql('DROP INDEX IDX_D69CA2E061220EA6');
        $this->addSql('CREATE TEMPORARY TABLE __temp__gurubeer_order_version AS SELECT id, order_id, creator_id, version, state FROM gurubeer_order_version');
        $this->addSql('DROP TABLE gurubeer_order_version');
        $this->addSql('CREATE TABLE gurubeer_order_version (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, order_id INTEGER DEFAULT NULL, creator_id INTEGER DEFAULT NULL, version INTEGER NOT NULL, state VARCHAR(255) NOT NULL)');
        $this->addSql('INSERT INTO gurubeer_order_version (id, order_id, creator_id, version, state) SELECT id, order_id, creator_id, version, state FROM __temp__gurubeer_order_version');
        $this->addSql('DROP TABLE __temp__gurubeer_order_version');
        $this->addSql('CREATE INDEX IDX_D69CA2E08D9F6D38 ON gurubeer_order_version (order_id)');
        $this->addSql('CREATE INDEX IDX_D69CA2E061220EA6 ON gurubeer_order_version (creator_id)');
    }
}
