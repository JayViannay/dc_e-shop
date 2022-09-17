<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220917132417 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE ticket (id INT AUTO_INCREMENT NOT NULL, reference_order_id INT NOT NULL, article_id INT NOT NULL, qty INT NOT NULL, INDEX IDX_97A0ADA355953647 (reference_order_id), INDEX IDX_97A0ADA37294869C (article_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user_order (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', total DOUBLE PRECISION NOT NULL, INDEX IDX_17EB68C0A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE ticket ADD CONSTRAINT FK_97A0ADA355953647 FOREIGN KEY (reference_order_id) REFERENCES user_order (id)');
        $this->addSql('ALTER TABLE ticket ADD CONSTRAINT FK_97A0ADA37294869C FOREIGN KEY (article_id) REFERENCES article (id)');
        $this->addSql('ALTER TABLE user_order ADD CONSTRAINT FK_17EB68C0A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE ticket DROP FOREIGN KEY FK_97A0ADA355953647');
        $this->addSql('ALTER TABLE ticket DROP FOREIGN KEY FK_97A0ADA37294869C');
        $this->addSql('ALTER TABLE user_order DROP FOREIGN KEY FK_17EB68C0A76ED395');
        $this->addSql('DROP TABLE ticket');
        $this->addSql('DROP TABLE user_order');
    }
}
