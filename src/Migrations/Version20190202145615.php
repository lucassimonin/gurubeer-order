<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190202145615 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        $this->addSql('DROP TABLE ext_log_entries');
        $this->addSql('DROP TABLE ext_translations');
        $this->addSql('DROP INDEX UNIQ_8D93D649E7927C74');
        $this->addSql('DROP INDEX UNIQ_8D93D649989D9B62');
        $this->addSql('DROP INDEX UNIQ_8D93D649F85E0677');
        $this->addSql('CREATE TEMPORARY TABLE __temp__user AS SELECT id, first_name, last_name, username, password, email, roles, enabled, confirmation_token, created, updated FROM user');
        $this->addSql('DROP TABLE user');
        $this->addSql('CREATE TABLE user (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, first_name VARCHAR(255) DEFAULT NULL COLLATE BINARY, last_name VARCHAR(255) DEFAULT NULL COLLATE BINARY, username VARCHAR(25) NOT NULL COLLATE BINARY, password VARCHAR(64) NOT NULL COLLATE BINARY, email VARCHAR(60) NOT NULL COLLATE BINARY, roles CLOB NOT NULL COLLATE BINARY --(DC2Type:array)
        , enabled BOOLEAN NOT NULL, confirmation_token VARCHAR(255) DEFAULT NULL COLLATE BINARY, created DATETIME NOT NULL, updated DATETIME NOT NULL)');
        $this->addSql('INSERT INTO user (id, first_name, last_name, username, password, email, roles, enabled, confirmation_token, created, updated) SELECT id, first_name, last_name, username, password, email, roles, enabled, confirmation_token, created, updated FROM __temp__user');
        $this->addSql('DROP TABLE __temp__user');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D649E7927C74 ON user (email)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D649F85E0677 ON user (username)');
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
        $this->addSql('CREATE TABLE ext_log_entries (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, "action" VARCHAR(8) NOT NULL COLLATE BINARY, logged_at DATETIME NOT NULL, object_id VARCHAR(64) DEFAULT NULL COLLATE BINARY, object_class VARCHAR(255) NOT NULL COLLATE BINARY, version INTEGER NOT NULL, username VARCHAR(255) DEFAULT NULL COLLATE BINARY, data CLOB DEFAULT \'NULL --(DC2Type:array)\' COLLATE BINARY --(DC2Type:array)
        )');
        $this->addSql('CREATE INDEX log_version_lookup_idx ON ext_log_entries (object_id, object_class, version)');
        $this->addSql('CREATE INDEX log_user_lookup_idx ON ext_log_entries (username)');
        $this->addSql('CREATE INDEX log_date_lookup_idx ON ext_log_entries (logged_at)');
        $this->addSql('CREATE INDEX log_class_lookup_idx ON ext_log_entries (object_class)');
        $this->addSql('CREATE TABLE ext_translations (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, locale VARCHAR(8) NOT NULL COLLATE BINARY, object_class VARCHAR(255) NOT NULL COLLATE BINARY, field VARCHAR(32) NOT NULL COLLATE BINARY, foreign_key VARCHAR(64) NOT NULL COLLATE BINARY, content CLOB DEFAULT NULL COLLATE BINARY)');
        $this->addSql('CREATE UNIQUE INDEX lookup_unique_idx ON ext_translations (locale, object_class, field, foreign_key)');
        $this->addSql('CREATE INDEX translations_lookup_idx ON ext_translations (locale, object_class, foreign_key)');
        $this->addSql('DROP INDEX IDX_87D5A3E38D9F6D38');
        $this->addSql('CREATE TEMPORARY TABLE __temp__gurubeer_item AS SELECT id, order_id, name, state, type, quantity, quantity_updated FROM gurubeer_item');
        $this->addSql('DROP TABLE gurubeer_item');
        $this->addSql('CREATE TABLE gurubeer_item (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, order_id INTEGER DEFAULT NULL, name VARCHAR(255) NOT NULL, state VARCHAR(255) NOT NULL, type VARCHAR(255) NOT NULL, quantity INTEGER NOT NULL, quantity_updated INTEGER NOT NULL)');
        $this->addSql('INSERT INTO gurubeer_item (id, order_id, name, state, type, quantity, quantity_updated) SELECT id, order_id, name, state, type, quantity, quantity_updated FROM __temp__gurubeer_item');
        $this->addSql('DROP TABLE __temp__gurubeer_item');
        $this->addSql('CREATE INDEX IDX_87D5A3E38D9F6D38 ON gurubeer_item (order_id)');
        $this->addSql('DROP INDEX IDX_D69CA2E08D9F6D38');
        $this->addSql('DROP INDEX IDX_D69CA2E061220EA6');
        $this->addSql('CREATE TEMPORARY TABLE __temp__gurubeer_order_version AS SELECT id, order_id, creator_id, version, state FROM gurubeer_order_version');
        $this->addSql('DROP TABLE gurubeer_order_version');
        $this->addSql('CREATE TABLE gurubeer_order_version (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, order_id INTEGER DEFAULT NULL, creator_id INTEGER DEFAULT NULL, version INTEGER NOT NULL, state VARCHAR(255) NOT NULL, created DATETIME NOT NULL, updated DATETIME NOT NULL)');
        $this->addSql('INSERT INTO gurubeer_order_version (id, order_id, creator_id, version, state) SELECT id, order_id, creator_id, version, state FROM __temp__gurubeer_order_version');
        $this->addSql('DROP TABLE __temp__gurubeer_order_version');
        $this->addSql('CREATE INDEX IDX_D69CA2E08D9F6D38 ON gurubeer_order_version (order_id)');
        $this->addSql('CREATE INDEX IDX_D69CA2E061220EA6 ON gurubeer_order_version (creator_id)');
        $this->addSql('DROP INDEX UNIQ_8D93D649F85E0677');
        $this->addSql('DROP INDEX UNIQ_8D93D649E7927C74');
        $this->addSql('CREATE TEMPORARY TABLE __temp__user AS SELECT id, first_name, last_name, username, password, email, roles, enabled, confirmation_token, created, updated FROM user');
        $this->addSql('DROP TABLE user');
        $this->addSql('CREATE TABLE user (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, first_name VARCHAR(255) DEFAULT NULL, last_name VARCHAR(255) DEFAULT NULL, username VARCHAR(25) NOT NULL, password VARCHAR(64) NOT NULL, email VARCHAR(60) NOT NULL, roles CLOB NOT NULL --(DC2Type:array)
        , enabled BOOLEAN NOT NULL, confirmation_token VARCHAR(255) DEFAULT NULL, created DATETIME NOT NULL, updated DATETIME NOT NULL, slug VARCHAR(255) NOT NULL COLLATE BINARY, created_by VARCHAR(255) DEFAULT NULL COLLATE BINARY, updated_by VARCHAR(255) DEFAULT NULL COLLATE BINARY)');
        $this->addSql('INSERT INTO user (id, first_name, last_name, username, password, email, roles, enabled, confirmation_token, created, updated) SELECT id, first_name, last_name, username, password, email, roles, enabled, confirmation_token, created, updated FROM __temp__user');
        $this->addSql('DROP TABLE __temp__user');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D649F85E0677 ON user (username)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D649E7927C74 ON user (email)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D649989D9B62 ON user (slug)');
    }
}
