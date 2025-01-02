<?php declare(strict_types=1);

namespace Gisl\GislBlog\Core\Content\GislBlogTranslation;

use Shopware\Core\Framework\DataAbstractionLayer\Entity;
use Shopware\Core\Framework\DataAbstractionLayer\EntityIdTrait;

class GislBlogTranslationEntity extends Entity
{
    use EntityIdTrait;

    protected ?string $title = null;
    protected ?string $short_description = null;
    protected ?string $description = null;
    protected ?string $type = null; // 'blog', 'category', or 'other'
    protected ?bool $active = null;

    protected ?string $slug;
    
    protected ?string $metaTitle;

    protected ?string $metaDescription;

    protected ?string $metaKeywords;

    public function getslug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(?string $slug): void
    {
        $this->slug = $slug;
    }

    // Title
    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(?string $title): void
    {
        $this->title = $title;
    }

    // Short Description
    public function getShortDescription(): ?string
    {
        return $this->short_description;
    }

    public function setShortDescription(?string $short_description): void
    {
        $this->short_description = $short_description;
    }

    // Description
    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): void
    {
        $this->description = $description;
    }

    // Type
    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(?string $type): void
    {
        $this->type = $type;
    }

    // Active Status
    public function isActive(): ?bool
    {
        return $this->active;
    }

    public function setActive(?bool $active): void
    {
        $this->active = $active;
    }

    
    public function getMetaTitle(): ?string
    {
        return $this->meta_title;
    }

    public function setMetaTitle(?string $metaTitle): void
    {
        $this->meta_title = $metaTitle;
    }

    public function getMetaDescription(): ?string
    {
        return $this->meta_description;
    }

    public function setMetaDescription(?string $metaDescription): void
    {
        $this->meta_description = $metaDescription;
    }
    public function getMetaKeywords(): ?string
    {
        return $this->meta_keywords;
    }

    public function setMetaKeywords(?string $metaKeywords): void
    {
        $this->meta_keywords = $metaKeywords;
    }
}
