import template from './sw-cms-preview-gisl-blog-listing.html.twig';
import './sw-cms-preview-gisl-blog-listing.scss';

Shopware.Component.register('sw-cms-preview-gisl-blog-listing', {
    template,
    computed: {
        assetFilter() {
            return Shopware.Filter.getByName('asset');
        },
    }
});