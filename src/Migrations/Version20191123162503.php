<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20191123162503 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE tournament_user');
        $this->addSql('ALTER TABLE game CHANGE team1_id team1_id INT DEFAULT NULL, CHANGE team2_id team2_id INT DEFAULT NULL, CHANGE next_game_id next_game_id INT DEFAULT NULL, CHANGE points_team1 points_team1 JSON NOT NULL, CHANGE points_team2 points_team2 JSON NOT NULL, CHANGE first_in_next_game first_in_next_game TINYINT(1) DEFAULT NULL');
        $this->addSql('ALTER TABLE player CHANGE phone phone VARCHAR(20) DEFAULT NULL, CHANGE email email VARCHAR(255) DEFAULT NULL, CHANGE path_to_logo path_to_logo VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE tournament ADD admin_id INT DEFAULT NULL, CHANGE date date DATE DEFAULT NULL, CHANGE price price INT DEFAULT NULL, CHANGE field_count field_count INT DEFAULT NULL, CHANGE max_teams_count max_teams_count INT DEFAULT NULL');
        $this->addSql('ALTER TABLE tournament ADD CONSTRAINT FK_BD5FB8D9642B8210 FOREIGN KEY (admin_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_BD5FB8D9642B8210 ON tournament (admin_id)');
        $this->addSql('ALTER TABLE user CHANGE roles roles JSON NOT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE tournament_user (tournament_id INT NOT NULL, user_id INT NOT NULL, INDEX IDX_BA1E647733D1A3E7 (tournament_id), INDEX IDX_BA1E6477A76ED395 (user_id), PRIMARY KEY(tournament_id, user_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE tournament_user ADD CONSTRAINT FK_BA1E647733D1A3E7 FOREIGN KEY (tournament_id) REFERENCES tournament (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE tournament_user ADD CONSTRAINT FK_BA1E6477A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE game CHANGE team1_id team1_id INT DEFAULT NULL, CHANGE team2_id team2_id INT DEFAULT NULL, CHANGE next_game_id next_game_id INT DEFAULT NULL, CHANGE points_team1 points_team1 LONGTEXT NOT NULL COLLATE utf8mb4_bin, CHANGE points_team2 points_team2 LONGTEXT NOT NULL COLLATE utf8mb4_bin, CHANGE first_in_next_game first_in_next_game TINYINT(1) DEFAULT \'NULL\'');
        $this->addSql('ALTER TABLE player CHANGE phone phone VARCHAR(20) DEFAULT \'NULL\' COLLATE utf8mb4_unicode_ci, CHANGE email email VARCHAR(255) DEFAULT \'NULL\' COLLATE utf8mb4_unicode_ci, CHANGE path_to_logo path_to_logo VARCHAR(255) DEFAULT \'NULL\' COLLATE utf8mb4_unicode_ci');
        $this->addSql('ALTER TABLE tournament DROP FOREIGN KEY FK_BD5FB8D9642B8210');
        $this->addSql('DROP INDEX IDX_BD5FB8D9642B8210 ON tournament');
        $this->addSql('ALTER TABLE tournament DROP admin_id, CHANGE date date DATE DEFAULT \'NULL\', CHANGE price price INT DEFAULT NULL, CHANGE field_count field_count INT DEFAULT NULL, CHANGE max_teams_count max_teams_count INT DEFAULT NULL');
        $this->addSql('ALTER TABLE user CHANGE roles roles LONGTEXT NOT NULL COLLATE utf8mb4_bin');
    }
}
