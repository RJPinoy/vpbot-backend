<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250801212901 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TABLE assistant (id INT AUTO_INCREMENT NOT NULL, private_chatbot_id INT DEFAULT NULL, name VARCHAR(255) DEFAULT NULL, INDEX IDX_C2997CD114416FF7 (private_chatbot_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE assistant ADD CONSTRAINT FK_C2997CD114416FF7 FOREIGN KEY (private_chatbot_id) REFERENCES private_chatbot (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE messages RENAME INDEX idx_db021e966d97254f TO IDX_DB021E96831651ED
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE messages RENAME INDEX idx_db021e9659546220 TO IDX_DB021E9614416FF7
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE private_chatbot DROP assistant
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE user RENAME INDEX uniq_8d93d649664d2890 TO UNIQ_8D93D649BFEF6256
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE assistant DROP FOREIGN KEY FK_C2997CD114416FF7
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE assistant
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE `user` RENAME INDEX uniq_8d93d649bfef6256 TO UNIQ_8D93D649664D2890
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE messages RENAME INDEX idx_db021e9614416ff7 TO IDX_DB021E9659546220
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE messages RENAME INDEX idx_db021e96831651ed TO IDX_DB021E966D97254F
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE private_chatbot ADD assistant VARCHAR(255) NOT NULL
        SQL);
    }
}
