<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240423153541 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE reservation');
        $this->addSql('DROP TABLE restaurant');
        $this->addSql('ALTER TABLE produit DROP FOREIGN KEY produit_ibfk_1');
        $this->addSql('ALTER TABLE produit ADD prix_apres_promo DOUBLE PRECISION DEFAULT NULL, CHANGE id_cat id_cat INT DEFAULT NULL, CHANGE image image VARCHAR(255) NOT NULL');
        $this->addSql('DROP INDEX id_cat ON produit');
        $this->addSql('CREATE INDEX IDX_29A5EC27FAABF2 ON produit (id_cat)');
        $this->addSql('ALTER TABLE produit ADD CONSTRAINT produit_ibfk_1 FOREIGN KEY (id_cat) REFERENCES categorie (id_cat)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE reservation (id_Reservation INT AUTO_INCREMENT NOT NULL, nom_resto VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_general_ci`, nom_client VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_general_ci`, tel_client INT NOT NULL, nbr_personnes INT NOT NULL, date_reservation DATE NOT NULL, statut VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_general_ci`, restaurant_id INT NOT NULL, PRIMARY KEY(id_Reservation)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_general_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE restaurant (id_Resto INT AUTO_INCREMENT NOT NULL, nom_resto VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_general_ci`, adresse_resto VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_general_ci`, description VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_general_ci`, PRIMARY KEY(id_Resto)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_general_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE produit DROP FOREIGN KEY FK_29A5EC27FAABF2');
        $this->addSql('ALTER TABLE produit DROP prix_apres_promo, CHANGE id_cat id_cat INT NOT NULL, CHANGE image image LONGTEXT NOT NULL');
        $this->addSql('DROP INDEX idx_29a5ec27faabf2 ON produit');
        $this->addSql('CREATE INDEX id_cat ON produit (id_cat)');
        $this->addSql('ALTER TABLE produit ADD CONSTRAINT FK_29A5EC27FAABF2 FOREIGN KEY (id_cat) REFERENCES categorie (id_cat)');
    }
}
