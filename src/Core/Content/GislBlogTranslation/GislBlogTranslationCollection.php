<?php declare(strict_types=1);

namespace Gisl\GislBlog\Core\Content\GislBlogTranslation;

use Shopware\Core\Framework\DataAbstractionLayer\EntityCollection;

/**
 * @method void add(GislBlogTranslationEntity $entity)
 * @method void set(string $key, GislBlogTranslationEntity $entity)
 * @method GislBlogTranslationEntity[] getIterator()
 * @method GislBlogTranslationEntity[] getElements()
 * @method GislBlogTranslationEntity|null get(string $key)
 * @method GislBlogTranslationEntity|null first()
 * @method GislBlogTranslationEntity|null last()
 */
class GislBlogTranslationCollection extends EntityCollection
{
    protected function getExpectedClass(): string
    {
        return GislBlogTranslationEntity::class;
    }
}
