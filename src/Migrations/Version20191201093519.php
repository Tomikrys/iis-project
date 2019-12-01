<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20191201093519 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE exp (id INT AUTO_INCREMENT NOT NULL, tournament_id INT NOT NULL, team_id INT NOT NULL, exp INT NOT NULL, INDEX IDX_6BE521B33D1A3E7 (tournament_id), INDEX IDX_6BE521B296CD8AE (team_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE exp ADD CONSTRAINT FK_6BE521B33D1A3E7 FOREIGN KEY (tournament_id) REFERENCES tournament (id)');
        $this->addSql('ALTER TABLE exp ADD CONSTRAINT FK_6BE521B296CD8AE FOREIGN KEY (team_id) REFERENCES team (id)');
        $this->addSql('ALTER TABLE game CHANGE team1_id team1_id INT DEFAULT NULL, CHANGE team2_id team2_id INT DEFAULT NULL, CHANGE next_game_id next_game_id INT DEFAULT NULL, CHANGE points_team1 points_team1 JSON NOT NULL, CHANGE points_team2 points_team2 JSON NOT NULL, CHANGE first_in_next_game first_in_next_game TINYINT(1) DEFAULT NULL');
        $this->addSql('ALTER TABLE player CHANGE admin_id admin_id INT DEFAULT NULL, CHANGE phone phone VARCHAR(20) DEFAULT NULL, CHANGE email email VARCHAR(255) DEFAULT NULL, CHANGE path_to_logo path_to_logo VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE team CHANGE admin_id admin_id INT DEFAULT NULL, CHANGE exp exp INT DEFAULT 0 NOT NULL');
        $this->addSql('ALTER TABLE tournament CHANGE admin_id admin_id INT DEFAULT NULL, CHANGE date date DATE DEFAULT NULL, CHANGE price price INT DEFAULT NULL, CHANGE field_count field_count INT DEFAULT NULL, CHANGE max_teams_count max_teams_count INT DEFAULT NULL');
        $this->addSql('ALTER TABLE user CHANGE roles roles JSON NOT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE exp');
        $this->addSql('ALTER TABLE game CHANGE team1_id team1_id INT DEFAULT NULL, CHANGE team2_id team2_id INT DEFAULT NULL, CHANGE next_game_id next_game_id INT DEFAULT NULL, CHANGE points_team1 points_team1 LONGTEXT NOT NULL COLLATE utf8mb4_bin, CHANGE points_team2 points_team2 LONGTEXT NOT NULL COLLATE utf8mb4_bin, CHANGE first_in_next_game first_in_next_game TINYINT(1) DEFAULT \'NULL\'');
        $this->addSql('ALTER TABLE player CHANGE admin_id admin_id INT DEFAULT NULL, CHANGE phone phone VARCHAR(20) DEFAULT \'NULL\' COLLATE utf8mb4_unicode_ci, CHANGE email email VARCHAR(255) DEFAULT \'NULL\' COLLATE utf8mb4_unicode_ci, CHANGE path_to_logo path_to_logo VARCHAR(255) DEFAULT \'NULL\' COLLATE utf8mb4_unicode_ci');
        $this->addSql('ALTER TABLE team CHANGE admin_id admin_id INT DEFAULT NULL, CHANGE exp exp INT DEFAULT NULL');
        $this->addSql('ALTER TABLE tournament CHANGE admin_id admin_id INT DEFAULT NULL, CHANGE date date DATE DEFAULT \'NULL\', CHANGE price price INT DEFAULT NULL, CHANGE field_count field_count INT DEFAULT NULL, CHANGE max_teams_count max_teams_count INT DEFAULT NULL');
        $this->addSql('ALTER TABLE user CHANGE roles roles LONGTEXT NOT NULL COLLATE utf8mb4_bin');
    }
}
