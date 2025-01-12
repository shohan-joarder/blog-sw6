<?php declare(strict_types=1);

namespace Gisl\GislBlog\Core\Content\GislBlogTranslation;

use Shopware\Core\Framework\DataAbstractionLayer\Field\FkField;
use Shopware\Core\Framework\DataAbstractionLayer\EntityDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\FieldCollection;
use Shopware\Core\Framework\DataAbstractionLayer\Field\BoolField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\PrimaryKey;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\Required;
use Shopware\Core\Framework\DataAbstractionLayer\Field\IdField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\StringField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\LongTextField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\CreatedAtField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\UpdatedAtField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\AllowHtml;
class GislBlogTranslationDefinition extends EntityDefinition
{
    public const ENTITY_NAME = 'gisl_blog_translation';

    public function getEntityName(): string
    {
        return self::ENTITY_NAME;
    }

    public function getEntityClass(): string
    {
        return GislBlogTranslationEntity::class;
    }

    public function getCollectionClass(): string
    {
        return GislBlogTranslationCollection::class;
    }

    protected function defineFields(): FieldCollection
    {
        return new FieldCollection([
            // Primary Key ID
            (new IdField('id', 'id'))->addFlags(new Required(), new PrimaryKey()),
    
            // Foreign Key for Language ID
            (new IdField('languageId', 'languageId'))->addFlags(new Required()),

            (new StringField('slug', 'slug')),
            
            (new StringField('meta_title', 'meta_title')),
            
            (new LongTextField('meta_description', 'meta_description')),
            
            (new LongTextField('meta_keywords', 'meta_keywords')),

            (new StringField('meta_title', 'metaTitle')),
            (new LongTextField('meta_description', 'metaDescription')),
            (new LongTextField('meta_keywords', 'metaKeywords')),
    
            // Foreign Key for Blog or Category (Polymorphic)
            (new IdField('fkId', 'fkId'))->addFlags(new Required()),
    
            // Type ENUM ('blog', 'category', 'other')
            (new StringField('type', 'type'))->addFlags(new Required()),
    
            // Title Field
            (new StringField('title', 'title'))->addFlags(new Required()),
    
            // Short Description
            (new LongTextField('short_description', 'short_description')),
            
            (new LongTextField('short_description', 'shortDescription')),
    
            // Description
            // (new LongTextField('description', 'description')),

            (new LongTextField('description', 'description',[
                'allowHtml' => true, // Custom handling in backend
            ]))->addFlags(new AllowHtml()),
    
            // Created At Field
            (new CreatedAtField()),
    
            // Updated At Field
            (new UpdatedAtField()),
        ]);
    }
    
}
