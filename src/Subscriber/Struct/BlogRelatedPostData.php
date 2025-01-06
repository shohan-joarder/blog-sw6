<?php
namespace Gisl\GislBlog\Subscriber\Struct;

use Shopware\Core\Framework\Struct\Struct;

class BlogRelatedPostData extends Struct
{
    private $relatedBlogs;
    
    public function __construct($relatedBlogs){
        $this->relatedBlogs = $relatedBlogs;
    }

    public function getRelatedBlogs()
    {
        return $this->relatedBlogs;
    }
}