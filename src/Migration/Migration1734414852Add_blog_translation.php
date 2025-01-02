<?php declare(strict_types=1);

namespace Gisl\GislBlog\Migration;

use Doctrine\DBAL\Connection;
use Shopware\Core\Framework\Migration\MigrationStep;

/**
 * @internal
 */
class Migration1734414852Add_blog_translation extends MigrationStep
{
    public function getCreationTimestamp(): int
    {
        return 1734414852;
    }

    public function update(Connection $connection): void
    {
        $sql = <<<SQL
            CREATE TABLE IF NOT EXISTS `gisl_blog_translation` (
                `id` BINARY(16) NOT NULL,
                `languageId` BINARY(16) NOT NULL,
                `fkId` BINARY(16) NOT NULL,
                `type` ENUM('blog', 'category', 'other') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'blog',
                `title` VARCHAR(255) COLLATE utf8mb4_unicode_ci NOT NULL,
                `slug` VARCHAR(255) NULL,
                `short_description` LONGTEXT COLLATE utf8mb4_unicode_ci NULL,
                `description` LONGTEXT COLLATE utf8mb4_unicode_ci NULL,
                `meta_title`        VARCHAR(255)  NULL,
                `meta_description`  LONGTEXT  NULL,
                `meta_keywords`     LONGTEXT  NULL,
                `created_at` DATETIME(3) NOT NULL,
                `updated_at` DATETIME(3) NULL,
                PRIMARY KEY (`id`),
                CONSTRAINT `fk.gisl_blog_translation.languageId` FOREIGN KEY (`languageId`)
                    REFERENCES `language` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
            ) ENGINE = InnoDB
                DEFAULT CHARSET = utf8mb4
            COLLATE = utf8mb4_unicode_ci;
        SQL;

        $connection->executeStatement($sql);
    }
}
