<?php declare(strict_types=1);

namespace Gisl\GislBlog\Core\Content\GislBlogPost;

use Shopware\Core\Framework\DataAbstractionLayer\Entity;
use Shopware\Core\Framework\DataAbstractionLayer\EntityIdTrait;

class GislBlogPostEntity extends Entity
{
    use EntityIdTrait;

    protected ?\DateTimeInterface $publishedAt;

    protected bool $active;

    protected ?array $tags = null;

    protected ?array $categories = null;

    protected ?array $tagsName = null;

    public function getPublishedAt(): ?\DateTimeImmutable
    {
        return $this->publishedAt;
    }

    public function setPublishedAt( $publishedAt): void
    {
        $this->publishedAt = $publishedAt;
    }

    public function isActive(): bool
    {
        return $this->active;
    }

    public function setActive(bool $active): void
    {
        $this->active = $active;
    }


    public function getTags(): ?array
    {
        return $this->tags;
    }

    public function setTags(?array $tags): void
    {
        $this->tags = $tags;
    }

    public function getCategories(): ?array
    {
        return $this->categories;
    }

    public function setCategories(?array $categories): void
    {
        $this->categories = $categories;
    }

    public function getTagsName(): ?array
    {
        return $this->tags_name;
    }

    public function setTagsName(?array $tagsName): void
    {
        $this->tags_name = $tagsName;
    }
}