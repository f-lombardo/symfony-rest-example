<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20241228175213 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'First DB migration';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE book (uuid UUID NOT NULL, title TEXT NOT NULL, author TEXT NOT NULL, published_date TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, isbn VARCHAR(17) NOT NULL, PRIMARY KEY(uuid))');
        $this->addSql('COMMENT ON COLUMN book.uuid IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN book.published_date IS \'(DC2Type:datetime_immutable)\'');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE book');
    }
}
