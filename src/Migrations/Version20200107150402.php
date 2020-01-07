<?php

declare(strict_types=1);

/*
 * This file is part of itk-dev/iot-crawler-adapter.
 *
 * (c) 2020 ITK Development
 *
 * This source file is subject to the MIT license.
 */

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200107150402 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE device (id VARCHAR(64) NOT NULL, name VARCHAR(255) NOT NULL, created_at DATETIME DEFAULT NULL, updated_at DATETIME DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE sensor (id VARCHAR(64) NOT NULL, device_id VARCHAR(64) NOT NULL, name VARCHAR(255) NOT NULL, INDEX IDX_BC8617B094A4C7D4 (device_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE measurement (id CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', sensor_id VARCHAR(64) NOT NULL, sequence_number INT NOT NULL, timestamp BIGINT NOT NULL, payload LONGTEXT NOT NULL COMMENT \'(DC2Type:json_array)\', INDEX IDX_2CE0D811A247991F (sensor_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE sensor ADD CONSTRAINT FK_BC8617B094A4C7D4 FOREIGN KEY (device_id) REFERENCES device (id)');
        $this->addSql('ALTER TABLE measurement ADD CONSTRAINT FK_2CE0D811A247991F FOREIGN KEY (sensor_id) REFERENCES sensor (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE sensor DROP FOREIGN KEY FK_BC8617B094A4C7D4');
        $this->addSql('ALTER TABLE measurement DROP FOREIGN KEY FK_2CE0D811A247991F');
        $this->addSql('DROP TABLE device');
        $this->addSql('DROP TABLE sensor');
        $this->addSql('DROP TABLE measurement');
    }
}
