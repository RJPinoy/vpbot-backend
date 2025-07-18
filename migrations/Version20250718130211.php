<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250718130211 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TABLE messages (id INT AUTO_INCREMENT NOT NULL, user_messages_id INT DEFAULT NULL, shared_chatbot_id INT DEFAULT NULL, personal_chatbot_id INT DEFAULT NULL, message LONGTEXT DEFAULT NULL, response LONGTEXT DEFAULT NULL, created_at DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)', INDEX IDX_DB021E96C8B96CF (user_messages_id), INDEX IDX_DB021E966D97254F (shared_chatbot_id), INDEX IDX_DB021E9659546220 (personal_chatbot_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE personal_chatbot (id INT AUTO_INCREMENT NOT NULL, hashed_api_key VARCHAR(255) NOT NULL, assistant VARCHAR(255) NOT NULL, instructions LONGTEXT DEFAULT NULL, model VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE shared_chatbot (id INT AUTO_INCREMENT NOT NULL, hashed_api_key VARCHAR(255) NOT NULL, assistant_id VARCHAR(255) NOT NULL, model VARCHAR(255) NOT NULL, name VARCHAR(255) DEFAULT NULL, icon_url VARCHAR(255) DEFAULT NULL, font_color1 VARCHAR(255) DEFAULT NULL, font_color2 VARCHAR(255) DEFAULT NULL, main_color VARCHAR(255) DEFAULT NULL, secondary_color VARCHAR(255) DEFAULT NULL, render_every_pages TINYINT(1) NOT NULL, position VARCHAR(255) NOT NULL, welcome_message VARCHAR(255) DEFAULT NULL, prompt_message VARCHAR(255) DEFAULT NULL, show_desktop TINYINT(1) DEFAULT NULL, show_tablet TINYINT(1) DEFAULT NULL, show_mobile TINYINT(1) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE `user` (id INT AUTO_INCREMENT NOT NULL, personal_bot_id INT DEFAULT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, username VARCHAR(50) NOT NULL, created_at DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)', last_connected DATETIME DEFAULT NULL, picture VARCHAR(255) DEFAULT NULL, UNIQUE INDEX UNIQ_8D93D649664D2890 (personal_bot_id), UNIQUE INDEX UNIQ_IDENTIFIER_EMAIL (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE messages ADD CONSTRAINT FK_DB021E96C8B96CF FOREIGN KEY (user_messages_id) REFERENCES `user` (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE messages ADD CONSTRAINT FK_DB021E966D97254F FOREIGN KEY (shared_chatbot_id) REFERENCES shared_chatbot (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE messages ADD CONSTRAINT FK_DB021E9659546220 FOREIGN KEY (personal_chatbot_id) REFERENCES personal_chatbot (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE `user` ADD CONSTRAINT FK_8D93D649664D2890 FOREIGN KEY (personal_bot_id) REFERENCES personal_chatbot (id)
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE messages DROP FOREIGN KEY FK_DB021E96C8B96CF
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE messages DROP FOREIGN KEY FK_DB021E966D97254F
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE messages DROP FOREIGN KEY FK_DB021E9659546220
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE `user` DROP FOREIGN KEY FK_8D93D649664D2890
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE messages
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE personal_chatbot
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE shared_chatbot
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE `user`
        SQL);
    }
}
