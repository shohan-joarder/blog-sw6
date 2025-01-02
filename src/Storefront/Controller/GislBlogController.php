<?php
namespace Gisl\GislBlog\Storefront\Controller;

use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\System\SalesChannel\SalesChannelContext;
use Shopware\Core\System\SystemConfig\SystemConfigService;
use Shopware\Storefront\Controller\StorefrontController;
use Shopware\Storefront\Page\GenericPageLoader;
use Shopware\Core\Content\Cms\SalesChannel\SalesChannelCmsPageLoader;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;

#[Route(defaults: ['_routeScope' => ['storefront']])]
class GislBlogController extends StorefrontController
{
    private SystemConfigService $systemConfigService;
    private SalesChannelCmsPageLoader $cmsPageLoader;  // Correct service for CMS page loading
    private GenericPageLoader $genericPageLoader;

    // Inject the correct services
    public function __construct(
        SystemConfigService $systemConfigService,
        SalesChannelCmsPageLoader $cmsPageLoader,  // Ensure the correct class is injected
        GenericPageLoader $genericPageLoader
    ) {
        $this->systemConfigService = $systemConfigService;
        $this->cmsPageLoader = $cmsPageLoader;
        $this->genericPageLoader = $genericPageLoader;
    }

    #[Route(path: '/gisl-blog/{articleId}', name: 'gisl.frontend.blog.detail', methods: ['GET'])]
    public function detailAction(string $articleId,Request $request, SalesChannelContext $context): Response
    {
        // Retrieve the plugin configuration
        $pluginConfig = $this->systemConfigService->get('GislBlog.config');
    
        // Ensure that the CMS Blog Detail Page ID is configured
        if (empty($pluginConfig['cmsBlogDetailPage'])) {
            throw new \RuntimeException('CMS Blog Detail Page ID is not configured.');
        }
    
        $cmsPageId = $pluginConfig['cmsBlogDetailPage'];
    
        // Create a Criteria object to find the CMS page
        $criteria = new \Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria();
        $criteria->setIds([$cmsPageId]);
    
        // Load the CMS page using the SalesChannelCmsPageLoader
        $cmsPageResult = $this->cmsPageLoader->load($request, $criteria, $context);
    
        // Ensure that the CMS page result is not empty
        $cmsPage = $cmsPageResult->first();
        if (!$cmsPage) {
            throw new \RuntimeException('CMS page not found.');
        }
    
        // Load the generic page (header, footer, etc.)
        $page = $this->genericPageLoader->load($request, $context);
    
        // Set the CMS page to the page object
        // Load the generic page (header, footer, etc.)
        $page = $this->genericPageLoader->load($request, $context);

        $page->cmsPage = $cmsPage;
        // dd($cmsPage);

        // Render the CMS page with the template
        return $this->renderStorefront('@Storefront/storefront/page/content/index.html.twig', [
            'page' => $page,
            'articleId' => $articleId
        ]);
    }
    
    // #[Route(path: '/gisl-blog/{articleId}', name: 'gisl.frontend.blog.detail', methods: ['GET'])]
    // public function detailAction(Request $request, SalesChannelContext $context): Response
    // {

    //     $page = $this->blogPageLoader->load($request, $context);

    //     return $this->renderStorefront('@Storefront/storefront/page/content/index.html.twig', ['page' => $page]);

    // }

    // #[Route(path: '/gisl-blog/{articleId}', name: 'gisl.frontend.blog.detail', methods: ['GET'])]
    // public function detailAction(Request $request, SalesChannelContext $context): Response
    // {
    //     // Retrieve the plugin configuration
    //     $pluginConfig = $this->systemConfigService->get('GislBlog.config');
    
    //     // Ensure that the CMS Blog Detail Page ID is configured
    //     if (empty($pluginConfig['cmsBlogDetailPage'])) {
    //         throw new \RuntimeException('CMS Blog Detail Page ID is not configured.');
    //     }
    
    //     $cmsPageId = $pluginConfig['cmsBlogDetailPage'];
    
    //     // Create a Criteria object to find the CMS page
    //     $criteria = new \Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria();
    //     $criteria->setIds([$cmsPageId]);
    
    //     // Load the CMS page using the SalesChannelCmsPageLoader
    //     $cmsPage = $this->cmsPageLoader->load($request, $criteria, $context);
    

    //     if (!$cmsPage) {
    //         throw new \RuntimeException('CMS page not found.');
    //     }
    
    //     // Load the generic page (header, footer, etc.)
    //     $page = $this->genericPageLoader->load($request, $context, $cmsPage);
    
    //     // dd($page);
    //     // Render the CMS page with the template
    //     return $this->renderStorefront('@Storefront/storefront/page/content/index.html.twig', ['page' => $page]);
    // }
    
    
}