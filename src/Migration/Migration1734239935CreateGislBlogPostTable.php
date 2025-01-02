<?php declare(strict_types=1);

namespace Gisl\GislBlog\Migration;

use Doctrine\DBAL\Connection;
use Shopware\Core\Framework\Migration\MigrationStep;

class Migration1734239935CreateGislBlogPostTable extends MigrationStep
{
    public function getCreationTimestamp(): int
    {
        return 1734239935;
    }

    public function update(Connection $connection): void
    {
        $sql = <<<SQL
        CREATE TABLE IF NOT EXISTS `gisl_blog_post` (
            `id` BINARY(16) NOT NULL,
            `media_id` BINARY(16) NULL,
            `author_id` BINARY(16) NULL,
            `active` TINYINT(1) NOT NULL,
            `categories` JSON NULL,
            `tags` JSON NULL,
            `tags_name` JSON NULL,
            `published_at` TIMESTAMP NULL,
            `created_at` DATETIME(3) NOT NULL,
            `updated_at` DATETIME(3) NULL,
            PRIMARY KEY (`id`)
        )
        ENGINE = InnoDB
        DEFAULT CHARSET = utf8mb4
        COLLATE = utf8mb4_unicode_ci;
        SQL;

        $connection->executeStatement($sql);
    }

    public function updateDestructive(Connection $connection): void
    {
    }
}
