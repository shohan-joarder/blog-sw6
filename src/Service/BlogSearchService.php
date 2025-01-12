<?php

namespace Gisl\GislBlog\Service;

use Shopware\Core\Content\Product\SalesChannel\SalesChannelProductSearchRoute;
use Shopware\Storefront\Framework\Search\StorefrontSearchService;
use Symfony\Component\HttpFoundation\Request;
use Shopware\Core\Framework\Context;

class BlogSearchService extends StorefrontSearchService
{
    private StorefrontSearchService $decorated;

    public function __construct(StorefrontSearchService $decorated)
    {
        $this->decorated = $decorated;
    }

    public function search(Request $request, Context $context): array
    {
        // Get the original search results
        $searchResults = $this->decorated->search($request, $context);

        // Add blog search results
        $blogResults = $this->getBlogSearchResults($request, $context);

        // Merge blog results into the search results
        $searchResults['blogs'] = $blogResults;

        return $searchResults;
    }

    private function getBlogSearchResults(Request $request, Context $context): array
    {
        $term = $request->query->get('search', '');

        if (empty($term)) {
            return [];
        }

        // Query the blog repository
        $criteria = new \Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria();
        $criteria->addFilter(new \Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\ContainsFilter('title', $term));
        $criteria->setLimit(10);

        $blogRepository = $this->container->get('gisl_blog_translation.repository');

        $searchResults = $blogRepository->search($criteria, $context);

        $blogs = [];
        foreach ($searchResults->getEntities() as $blog) {
            $blogs[] = [
                'title' => $blog->getTitle(),
                'url'   => $this->generateBlogUrl($blog->getId()),
            ];
        }

        return $blogs;
    }

    private function generateBlogUrl(string $blogId): string
    {
        // Generate the URL for the blog post
        return $this->router->generate('frontend.blog.detail', ['id' => $blogId]);
    }
}
