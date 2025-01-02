<?php

namespace Gisl\GislBlog\Subscriber;

use Shopware\Core\Framework\Context;
use Shopware\Storefront\Page\Cms\CmsPageLoadedEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class BlogSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        return [
            CmsPageLoadedEvent::class => 'onCmsPageLoaded',
        ];
    }

    public function onCmsPageLoaded(CmsPageLoadedEvent $event): void
    {

        $context = $event->getContext();
        $pages = $event->getResult();

        foreach ($pages as $page) {
            // Example logic: Add some extension or data for CMS pages
            $page->addExtension('customBlogData', ['example_key' => 'example_value']);
        }
    }
}