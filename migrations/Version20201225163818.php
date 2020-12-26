<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20201225163818 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE todo_list (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, name VARCHAR(255) NOT NULL, description VARCHAR(255), UNIQUE INDEX UNIQ_1B199E07A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE todo_list ADD CONSTRAINT FK_1B199E07A76ED395 FOREIGN KEY (user_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE item ADD todo_list_id INT NOT NULL');
        $this->addSql('ALTER TABLE item ADD CONSTRAINT FK_1F1B251EE8A7DCFA FOREIGN KEY (todo_list_id) REFERENCES todo_list (id)');
        $this->addSql('CREATE INDEX IDX_1F1B251EE8A7DCFA ON item (todo_list_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE item DROP FOREIGN KEY FK_1F1B251EE8A7DCFA');
        $this->addSql('DROP TABLE todo_list');
        $this->addSql('DROP INDEX IDX_1F1B251EE8A7DCFA ON item');
        $this->addSql('ALTER TABLE item DROP todo_list_id');
    }
}
