import template from './sw-cms-preview-gisl-blog-short-description.html.twig';

Shopware.Component.register('sw-cms-preview-gisl-blog-short-description', {
    template,
    computed: {
        assetFilter() {
            return Shopware.Filter.getByName('asset');
        },
    }
});