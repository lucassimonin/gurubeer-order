<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190202132201 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'sqlite', 'Migration can only be executed safely on \'sqlite\'.');

        $this->addSql('CREATE TABLE gurubeer_order_version (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, order_id INTEGER DEFAULT NULL, creator_id INTEGER DEFAULT NULL, state VARCHAR(255) NOT NULL, created DATETIME NOT NULL, updated DATETIME NOT NULL)');
        $this->addSql('CREATE INDEX IDX_D69CA2E08D9F6D38 ON gurubeer_order_version (order_id)');
        $this->addSql('CREATE INDEX IDX_D69CA2E061220EA6 ON gurubeer_order_version (creator_id)');
        $this->addSql('DROP INDEX IDX_36BDD3BF61220EA6');
        $this->addSql('CREATE TEMPORARY TABLE __temp__gurubeer_order AS SELECT id, creator_id, name, file_name, created, updated FROM gurubeer_order');
        $this->addSql('DROP TABLE gurubeer_order');
        $this->addSql('CREATE TABLE gurubeer_order (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, creator_id INTEGER DEFAULT NULL, name VARCHAR(255) NOT NULL COLLATE BINARY, file_name VARCHAR(255) DEFAULT NULL COLLATE BINARY, created DATETIME NOT NULL, updated DATETIME NOT NULL, CONSTRAINT FK_36BDD3BF61220EA6 FOREIGN KEY (creator_id) REFERENCES user (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO gurubeer_order (id, creator_id, name, file_name, created, updated) SELECT id, creator_id, name, file_name, created, updated FROM __temp__gurubeer_order');
        $this->addSql('DROP TABLE __temp__gurubeer_order');
        $this->addSql('CREATE INDEX IDX_36BDD3BF61220EA6 ON gurubeer_order (creator_id)');
        $this->addSql('DROP INDEX IDX_87D5A3E38D9F6D38');
        $this->addSql('CREATE TEMPORARY TABLE __temp__gurubeer_item AS SELECT id, order_id, name, state, type, quantity, quantity_updated FROM gurubeer_item');
        $this->addSql('DROP TABLE gurubeer_item');
        $this->addSql('CREATE TABLE gurubeer_item (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, order_id INTEGER DEFAULT NULL, name VARCHAR(255) NOT NULL COLLATE BINARY, state VARCHAR(255) NOT NULL COLLATE BINARY, type VARCHAR(255) NOT NULL COLLATE BINARY, quantity INTEGER NOT NULL, quantity_updated INTEGER NOT NULL, CONSTRAINT FK_87D5A3E38D9F6D38 FOREIGN KEY (order_id) REFERENCES gurubeer_order_version (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO gurubeer_item (id, order_id, name, state, type, quantity, quantity_updated) SELECT id, order_id, name, state, type, quantity, quantity_updated FROM __temp__gurubeer_item');
        $this->addSql('DROP TABLE __temp__gurubeer_item');
        $this->addSql('CREATE INDEX IDX_87D5A3E38D9F6D38 ON gurubeer_item (order_id)');
        $this->addSql('DROP INDEX log_version_lookup_idx');
        $this->addSql('DROP INDEX log_user_lookup_idx');
        $this->addSql('DROP INDEX log_date_lookup_idx');
        $this->addSql('DROP INDEX log_class_lookup_idx');
        $this->addSql('CREATE TEMPORARY TABLE __temp__ext_log_entries AS SELECT id, "action", logged_at, object_id, object_class, version, data, username FROM ext_log_entries');
        $this->addSql('DROP TABLE ext_log_entries');
        $this->addSql('CREATE TABLE ext_log_entries (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, "action" VARCHAR(8) NOT NULL COLLATE BINARY, logged_at DATETIME NOT NULL, object_id VARCHAR(64) DEFAULT NULL COLLATE BINARY, object_class VARCHAR(255) NOT NULL COLLATE BINARY, version INTEGER NOT NULL, username VARCHAR(255) DEFAULT NULL COLLATE BINARY, data CLOB DEFAULT NULL --(DC2Type:array)
        )');
        $this->addSql('INSERT INTO ext_log_entries (id, "action", logged_at, object_id, object_class, version, data, username) SELECT id, "action", logged_at, object_id, object_class, version, data, username FROM __temp__ext_log_entries');
        $this->addSql('DROP TABLE __temp__ext_log_entries');
        $this->addSql('CREATE INDEX log_version_lookup_idx ON ext_log_entries (object_id, object_class, version)');
        $this->addSql('CREATE INDEX log_user_lookup_idx ON ext_log_entries (username)');
        $this->addSql('CREATE INDEX log_date_lookup_idx ON ext_log_entries (logged_at)');
        $this->addSql('CREATE INDEX log_class_lookup_idx ON ext_log_entries (object_class)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'sqlite', 'Migration can only be executed safely on \'sqlite\'.');

        $this->addSql('DROP TABLE gurubeer_order_version');
        $this->addSql('DROP INDEX log_class_lookup_idx');
        $this->addSql('DROP INDEX log_date_lookup_idx');
        $this->addSql('DROP INDEX log_user_lookup_idx');
        $this->addSql('DROP INDEX log_version_lookup_idx');
        $this->addSql('CREATE TEMPORARY TABLE __temp__ext_log_entries AS SELECT id, "action", logged_at, object_id, object_class, version, data, username FROM ext_log_entries');
        $this->addSql('DROP TABLE ext_log_entries');
        $this->addSql('CREATE TABLE ext_log_entries (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, "action" VARCHAR(8) NOT NULL, logged_at DATETIME NOT NULL, object_id VARCHAR(64) DEFAULT NULL, object_class VARCHAR(255) NOT NULL, version INTEGER NOT NULL, username VARCHAR(255) DEFAULT NULL, data CLOB DEFAULT \'NULL --(DC2Type:array)\' COLLATE BINARY --(DC2Type:array)
        )');
        $this->addSql('INSERT INTO ext_log_entries (id, "action", logged_at, object_id, object_class, version, data, username) SELECT id, "action", logged_at, object_id, object_class, version, data, username FROM __temp__ext_log_entries');
        $this->addSql('DROP TABLE __temp__ext_log_entries');
        $this->addSql('CREATE INDEX log_class_lookup_idx ON ext_log_entries (object_class)');
        $this->addSql('CREATE INDEX log_date_lookup_idx ON ext_log_entries (logged_at)');
        $this->addSql('CREATE INDEX log_user_lookup_idx ON ext_log_entries (username)');
        $this->addSql('CREATE INDEX log_version_lookup_idx ON ext_log_entries (object_id, object_class, version)');
        $this->addSql('DROP INDEX IDX_36BDD3BF61220EA6');
        $this->addSql('CREATE TEMPORARY TABLE __temp__gurubeer_order AS SELECT id, creator_id, name, file_name, created, updated FROM gurubeer_order');
        $this->addSql('DROP TABLE gurubeer_order');
        $this->addSql('CREATE TABLE gurubeer_order (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, creator_id INTEGER DEFAULT NULL, name VARCHAR(255) NOT NULL, file_name VARCHAR(255) DEFAULT NULL, created DATETIME NOT NULL, updated DATETIME NOT NULL, state VARCHAR(255) NOT NULL COLLATE BINARY, before_state VARCHAR(255) NOT NULL COLLATE BINARY)');
        $this->addSql('INSERT INTO gurubeer_order (id, creator_id, name, file_name, created, updated) SELECT id, creator_id, name, file_name, created, updated FROM __temp__gurubeer_order');
        $this->addSql('DROP TABLE __temp__gurubeer_order');
        $this->addSql('CREATE INDEX IDX_36BDD3BF61220EA6 ON gurubeer_order (creator_id)');
    }
}
