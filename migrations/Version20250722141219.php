<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250722141219 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE deployment (id UUID NOT NULL, start_date DATE NOT NULL, end_date DATE DEFAULT NULL, name VARCHAR(128) NOT NULL, shift_needed INT DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('COMMENT ON COLUMN deployment.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN deployment.start_date IS \'(DC2Type:date_immutable)\'');
        $this->addSql('COMMENT ON COLUMN deployment.end_date IS \'(DC2Type:date_immutable)\'');
        $this->addSql('CREATE TABLE shift (id UUID NOT NULL, deployment_id UUID NOT NULL, date TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, worked DOUBLE PRECISION NOT NULL, pay NUMERIC(10, 2) DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_A50B3B459DF4CE98 ON shift (deployment_id)');
        $this->addSql('COMMENT ON COLUMN shift.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN shift.deployment_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN shift.date IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE shift ADD CONSTRAINT FK_A50B3B459DF4CE98 FOREIGN KEY (deployment_id) REFERENCES deployment (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE shift DROP CONSTRAINT FK_A50B3B459DF4CE98');
        $this->addSql('DROP TABLE deployment');
        $this->addSql('DROP TABLE shift');
    }
}
