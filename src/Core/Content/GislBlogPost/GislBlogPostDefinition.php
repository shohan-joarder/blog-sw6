<?php declare(strict_types=1);

namespace Gisl\GislBlog\Core\Content\GislBlogPost;

use Shopware\Core\Content\Media\MediaDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\Field\IdField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\FkField;
use Shopware\Core\Framework\DataAbstractionLayer\FieldCollection;
use Shopware\Core\Framework\DataAbstractionLayer\Field\BoolField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\JsonField;
use Shopware\Core\Framework\DataAbstractionLayer\EntityDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\Field\StringField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\LongTextField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\DateTimeField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\TranslationsAssociationField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\Required;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\PrimaryKey;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\AllowHtml;
use Shopware\Core\Framework\DataAbstractionLayer\Field\ManyToOneAssociationField;

use Gisl\GislBlog\Core\Content\GislBlogAuthor\GislBlogAuthorDefinition;
use Gisl\GislBlog\Core\Content\GislBlogTranslation\GislBlogTranslationDefinition;



class GislBlogPostDefinition extends EntityDefinition
{
    public const ENTITY_NAME = 'gisl_blog_post';

    public function getEntityName(): string
    {
        return self::ENTITY_NAME;
    }

    public function getEntityClass(): string
    {
        return GislBlogPostEntity::class;
    }

    public function getCollectionClass(): string
    {
        return GislBlogPostCollection::class;
    }

    protected function defineFields(): FieldCollection
    {
        return new FieldCollection([
            (new IdField('id', 'id'))->addFlags(new Required(), new PrimaryKey()),
            (new DateTimeField('published_at', 'publishedAt')),
            (new BoolField('active', 'active')),
            (new JsonField('tags', 'tags')),
            (new JsonField('categories', 'categories')),
            (new JsonField('tags_name', 'tags_name')),
            (new FkField('media_id', 'mediaId', MediaDefinition::class)),
            (new ManyToOneAssociationField('media', 'media_id', MediaDefinition::class, 'id', false)),
            (new FkField('author_id', 'authorId', GislBlogAuthorDefinition::class)),
            (new ManyToOneAssociationField('postAuthor', 'author_id', GislBlogAuthorDefinition::class, 'id')),

            // Association with translations
            (new TranslationsAssociationField(GislBlogTranslationDefinition::class, 'fkId'))
            
        ]);
    }
}
