<?php
namespace Gisl\GislBlog\Subscriber\Struct;

use Shopware\Core\Framework\Struct\Struct;

class BlogShortDescriptionData extends Struct
{
    private ?string $banner;
    private ?string $shortDescription;

    public function __construct(?string $banner, ?string $shortDescription)
    {
        $this->banner = $banner;
        $this->shortDescription = $shortDescription;
    }

    public function getBanner(): ?string
    {
        return $this->banner;
    }

    public function getShortDescription(): ?string
    {
        return $this->shortDescription;
    }
}
