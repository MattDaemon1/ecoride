<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241221203619 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE avis CHANGE note note VARCHAR(50) DEFAULT NULL, CHANGE statut statut VARCHAR(50) DEFAULT NULL');
        $this->addSql('ALTER TABLE configuration ADD id INT AUTO_INCREMENT NOT NULL, DROP id_configuration, CHANGE user_id user_id INT DEFAULT NULL, DROP PRIMARY KEY, ADD PRIMARY KEY (id)');
        $this->addSql('ALTER TABLE configuration ADD CONSTRAINT FK_A5E2A5D7A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE configuration RENAME INDEX fk_user TO IDX_A5E2A5D7A76ED395');
        $this->addSql('ALTER TABLE covoiturage ADD voiture_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE covoiturage ADD CONSTRAINT FK_28C79E89181A8BA FOREIGN KEY (voiture_id) REFERENCES voiture (id)');
        $this->addSql('CREATE INDEX IDX_28C79E89181A8BA ON covoiturage (voiture_id)');
        $this->addSql('ALTER TABLE parametre MODIFY parametre_id INT NOT NULL');
        $this->addSql('ALTER TABLE parametre DROP FOREIGN KEY fk_configuration');
        $this->addSql('DROP INDEX `primary` ON parametre');
        $this->addSql('ALTER TABLE parametre CHANGE configuration_id configuration_id INT DEFAULT NULL, CHANGE propriete propriete VARCHAR(50) DEFAULT NULL, CHANGE valeur valeur VARCHAR(50) DEFAULT NULL, CHANGE parametre_id id INT AUTO_INCREMENT NOT NULL');
        $this->addSql('ALTER TABLE parametre ADD CONSTRAINT FK_ACC7904173F32DD8 FOREIGN KEY (configuration_id) REFERENCES configuration (id)');
        $this->addSql('ALTER TABLE parametre ADD PRIMARY KEY (id)');
        $this->addSql('ALTER TABLE parametre RENAME INDEX fk_configuration TO IDX_ACC7904173F32DD8');
        $this->addSql('ALTER TABLE user CHANGE roles roles JSON NOT NULL, CHANGE telephone telephone VARCHAR(50) DEFAULT NULL, CHANGE adresse adresse VARCHAR(50) DEFAULT NULL, CHANGE date_naissance date_naissance DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE messenger_messages CHANGE delivered_at delivered_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\'');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE avis CHANGE note note VARCHAR(50) DEFAULT \'NULL\', CHANGE statut statut VARCHAR(50) DEFAULT \'NULL\'');
        $this->addSql('ALTER TABLE configuration MODIFY id INT NOT NULL');
        $this->addSql('ALTER TABLE configuration DROP FOREIGN KEY FK_A5E2A5D7A76ED395');
        $this->addSql('DROP INDEX `PRIMARY` ON configuration');
        $this->addSql('ALTER TABLE configuration ADD id_configuration INT NOT NULL, DROP id, CHANGE user_id user_id INT NOT NULL');
        $this->addSql('ALTER TABLE configuration ADD PRIMARY KEY (id_configuration)');
        $this->addSql('ALTER TABLE configuration RENAME INDEX idx_a5e2a5d7a76ed395 TO fk_user');
        $this->addSql('ALTER TABLE covoiturage DROP FOREIGN KEY FK_28C79E89181A8BA');
        $this->addSql('DROP INDEX IDX_28C79E89181A8BA ON covoiturage');
        $this->addSql('ALTER TABLE covoiturage DROP voiture_id');
        $this->addSql('ALTER TABLE messenger_messages CHANGE delivered_at delivered_at DATETIME DEFAULT \'NULL\' COMMENT \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE parametre MODIFY id INT NOT NULL');
        $this->addSql('ALTER TABLE parametre DROP FOREIGN KEY FK_ACC7904173F32DD8');
        $this->addSql('DROP INDEX `PRIMARY` ON parametre');
        $this->addSql('ALTER TABLE parametre CHANGE configuration_id configuration_id INT NOT NULL, CHANGE propriete propriete VARCHAR(50) NOT NULL, CHANGE valeur valeur VARCHAR(50) NOT NULL, CHANGE id parametre_id INT AUTO_INCREMENT NOT NULL');
        $this->addSql('ALTER TABLE parametre ADD CONSTRAINT fk_configuration FOREIGN KEY (configuration_id) REFERENCES configuration (id_configuration) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE parametre ADD PRIMARY KEY (parametre_id)');
        $this->addSql('ALTER TABLE parametre RENAME INDEX idx_acc7904173f32dd8 TO fk_configuration');
        $this->addSql('ALTER TABLE user CHANGE roles roles LONGTEXT NOT NULL COLLATE `utf8mb4_bin`, CHANGE telephone telephone VARCHAR(50) DEFAULT \'NULL\', CHANGE adresse adresse VARCHAR(50) DEFAULT \'NULL\', CHANGE date_naissance date_naissance DATETIME DEFAULT \'NULL\'');
    }
}
