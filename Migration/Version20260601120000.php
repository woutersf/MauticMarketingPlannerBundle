<?php

declare(strict_types=1);

namespace MauticPlugin\MauticMarketingPlannerBundle\Migration;

use Doctrine\DBAL\Schema\Schema;
use Mautic\CoreBundle\Doctrine\AbstractMauticMigration;

final class Version20260601120000 extends AbstractMauticMigration
{
    public function up(Schema $schema): void
    {
        $table = $this->prefix.'planner_items';

        if ($schema->hasTable($table)) {
            return;
        }

        $this->addSql(<<<SQL
CREATE TABLE `{$table}` (
    `id`             INT NOT NULL AUTO_INCREMENT,
    `name`           VARCHAR(255) NOT NULL,
    `description`    LONGTEXT,
    `created_at`     DATETIME NOT NULL,
    `deadline`       DATE NOT NULL,
    `done_at`        DATE DEFAULT NULL,
    `assigned_to_id` INT DEFAULT NULL,
    PRIMARY KEY (`id`),
    INDEX `idx_planner_deadline` (`deadline`),
    INDEX `idx_planner_assigned` (`assigned_to_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
SQL);
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE IF EXISTS `'.$this->prefix.'planner_items`');
    }
}
