<?php declare(strict_types=1);

namespace Gisl\GislBlog\Core\Content\GislBlogCategory;

use Shopware\Core\Content\Media\MediaDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\Field\IdField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\FkField;
use Shopware\Core\Framework\DataAbstractionLayer\EntityDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\FieldCollection;
use Shopware\Core\Framework\DataAbstractionLayer\Field\BoolField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\PrimaryKey;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\Required;
use Shopware\Core\Framework\DataAbstractionLayer\Field\StringField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\ManyToOneAssociationField;

use Shopware\Core\Framework\DataAbstractionLayer\Field\TranslationsAssociationField;
use Gisl\GislBlog\Core\Content\GislBlogTranslation\GislBlogTranslationDefinition;

class GislBlogCategoryDefinition extends EntityDefinition
{
    public const ENTITY_NAME = 'gisl_blog_category';

    public function getEntityName(): string
    {
        return self::ENTITY_NAME;
    }

    public function getEntityClass(): string
    {
        return GislBlogCategoryEntity::class;
    }

    public function getCollectionClass(): string
    {
        return GislBlogCategoryCollection::class;
    }

    protected function defineFields(): FieldCollection
    {
        return new FieldCollection([
            (new IdField('id', 'id'))->addFlags(new Required(), new PrimaryKey()),
            // (new StringField('slug', 'slug')),
            (new BoolField('active', 'active')),
            // (new StringField('meta_title', 'meta_title')),
            // (new StringField('meta_description', 'meta_description')),
            // (new StringField('meta_keywords', 'meta_keywords')),
            (new FkField('media_id', 'mediaId', MediaDefinition::class)),
            (new ManyToOneAssociationField('media', 'media_id', MediaDefinition::class, 'id', false)),

            (new TranslationsAssociationField(GislBlogTranslationDefinition::class, 'fkId'))
        ]);
    }
}
