<?php
namespace Gisl\GislBlog\Subscriber\Struct;

use Shopware\Core\Framework\Struct\Struct;

class BlogDescriptionData extends Struct
{
    private ?string $title;
    private ?string $description;

    public function __construct(?string $title, ?string $description)
    {
        $this->title = $title;
        $this->description = $description;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }
}
