<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20191128114005 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE game (id INT AUTO_INCREMENT NOT NULL, team1_id INT DEFAULT NULL, team2_id INT DEFAULT NULL, tournament_id INT NOT NULL, next_game_id INT DEFAULT NULL, round INT NOT NULL, points_team1 JSON NOT NULL, points_team2 JSON NOT NULL, first_in_next_game TINYINT(1) DEFAULT NULL, INDEX IDX_232B318CE72BCFA4 (team1_id), INDEX IDX_232B318CF59E604A (team2_id), INDEX IDX_232B318C33D1A3E7 (tournament_id), INDEX IDX_232B318C2601F3A7 (next_game_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE player (id INT AUTO_INCREMENT NOT NULL, admin_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, is_girl TINYINT(1) NOT NULL, phone VARCHAR(20) DEFAULT NULL, email VARCHAR(255) DEFAULT NULL, path_to_logo VARCHAR(255) DEFAULT NULL, INDEX IDX_98197A65642B8210 (admin_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE team (id INT AUTO_INCREMENT NOT NULL, admin_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, INDEX IDX_C4E0A61F642B8210 (admin_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE team_player (team_id INT NOT NULL, player_id INT NOT NULL, INDEX IDX_EE023DBC296CD8AE (team_id), INDEX IDX_EE023DBC99E6F5DF (player_id), PRIMARY KEY(team_id, player_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE tournament (id INT AUTO_INCREMENT NOT NULL, admin_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, date DATE DEFAULT NULL, price INT DEFAULT NULL, field_count INT DEFAULT NULL, max_teams_count INT DEFAULT NULL, plays_in_game INT NOT NULL, INDEX IDX_BD5FB8D9642B8210 (admin_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE tournament_team (tournament_id INT NOT NULL, team_id INT NOT NULL, INDEX IDX_F36D142133D1A3E7 (tournament_id), INDEX IDX_F36D1421296CD8AE (team_id), PRIMARY KEY(tournament_id, team_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_8D93D649E7927C74 (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE game ADD CONSTRAINT FK_232B318CE72BCFA4 FOREIGN KEY (team1_id) REFERENCES team (id)');
        $this->addSql('ALTER TABLE game ADD CONSTRAINT FK_232B318CF59E604A FOREIGN KEY (team2_id) REFERENCES team (id)');
        $this->addSql('ALTER TABLE game ADD CONSTRAINT FK_232B318C33D1A3E7 FOREIGN KEY (tournament_id) REFERENCES tournament (id)');
        $this->addSql('ALTER TABLE game ADD CONSTRAINT FK_232B318C2601F3A7 FOREIGN KEY (next_game_id) REFERENCES game (id)');
        $this->addSql('ALTER TABLE player ADD CONSTRAINT FK_98197A65642B8210 FOREIGN KEY (admin_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE team ADD CONSTRAINT FK_C4E0A61F642B8210 FOREIGN KEY (admin_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE team_player ADD CONSTRAINT FK_EE023DBC296CD8AE FOREIGN KEY (team_id) REFERENCES team (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE team_player ADD CONSTRAINT FK_EE023DBC99E6F5DF FOREIGN KEY (player_id) REFERENCES player (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE tournament ADD CONSTRAINT FK_BD5FB8D9642B8210 FOREIGN KEY (admin_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE tournament_team ADD CONSTRAINT FK_F36D142133D1A3E7 FOREIGN KEY (tournament_id) REFERENCES tournament (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE tournament_team ADD CONSTRAINT FK_F36D1421296CD8AE FOREIGN KEY (team_id) REFERENCES team (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE game DROP FOREIGN KEY FK_232B318C2601F3A7');
        $this->addSql('ALTER TABLE team_player DROP FOREIGN KEY FK_EE023DBC99E6F5DF');
        $this->addSql('ALTER TABLE game DROP FOREIGN KEY FK_232B318CE72BCFA4');
        $this->addSql('ALTER TABLE game DROP FOREIGN KEY FK_232B318CF59E604A');
        $this->addSql('ALTER TABLE team_player DROP FOREIGN KEY FK_EE023DBC296CD8AE');
        $this->addSql('ALTER TABLE tournament_team DROP FOREIGN KEY FK_F36D1421296CD8AE');
        $this->addSql('ALTER TABLE game DROP FOREIGN KEY FK_232B318C33D1A3E7');
        $this->addSql('ALTER TABLE tournament_team DROP FOREIGN KEY FK_F36D142133D1A3E7');
        $this->addSql('ALTER TABLE player DROP FOREIGN KEY FK_98197A65642B8210');
        $this->addSql('ALTER TABLE team DROP FOREIGN KEY FK_C4E0A61F642B8210');
        $this->addSql('ALTER TABLE tournament DROP FOREIGN KEY FK_BD5FB8D9642B8210');
        $this->addSql('DROP TABLE game');
        $this->addSql('DROP TABLE player');
        $this->addSql('DROP TABLE team');
        $this->addSql('DROP TABLE team_player');
        $this->addSql('DROP TABLE tournament');
        $this->addSql('DROP TABLE tournament_team');
        $this->addSql('DROP TABLE user');
    }
}
