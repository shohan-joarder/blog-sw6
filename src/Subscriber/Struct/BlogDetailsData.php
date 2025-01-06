<?php
namespace Gisl\GislBlog\Subscriber\Struct;

use Shopware\Core\Framework\Struct\Struct;

class BlogDetailsData extends Struct
{
    private ?string $title;
    private ?string $description;
    private ?string $toc;  // Table of Contents
    private ?string $author;
    private ?string $authorImage;
    private ?string $banner;
    private $publishedAt;  // Published date
    private $allCategory;
    private $blogCategory;

    // Constructor to initialize the properties
    public function __construct(
        ?string $title,
        ?string $description,
        ?string $toc,
        ?string $author,
        ?string $authorImage,
        ?string $banner,
         $publishedAt,
         $allCategory,
         $blogCategory,
         $catName
    ) {
        $this->title = $title;
        $this->description = $description;
        $this->toc = $toc;
        $this->author = $author;
        $this->authorImage = $authorImage;
        $this->banner = $banner;
        $this->publishedAt = $publishedAt;
        $this->allCategory = $allCategory;
        $this->blogCategory = $blogCategory;
        $this->catName = $catName;
    }

    // Getter methods
    public function getTitle(): ?string
    {
        return $this->title;
    }
    public function getAuthor(): ?string
    {
        return $this->author;
    }

    public function getAuthorImage(): ?string
    {
        return $this->authorImage;
    }

    public function getBanner(): ?string
    {
        return $this->banner;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function getToc(): ?string
    {
        return $this->toc;
    }

    public function getPublishedAt(): ?string
    {
        return $this->publishedAt;
    }

    // Setter methods (optional)
    public function setAuthor(?string $author): void
    {
        $this->author = $author;
    }

    public function setAuthorImage(?string $authorImage): void
    {
        $this->authorImage = $authorImage;
    }

    public function setBanner(?string $banner): void
    {
        $this->banner = $banner;
    }

    public function setDescription(?string $description): void
    {
        $this->description = $description;
    }

    public function setToc(?string $toc): void
    {
        $this->toc = $toc;
    }

    public function setPublishedAt(?string $publishedAt): void
    {
        $this->publishedAt = $publishedAt;
    }
    public function setAllCategory( $allCategory): void
    {
        $this->allCategory = $allCategory;
    }
    public function setBlogCategory( $blogCategory): void
    {
        $this->blogCategory = $blogCategory;
    }
    public function setCatName( $catName): void
    {
        $this->catName = $catName;
    }
}

