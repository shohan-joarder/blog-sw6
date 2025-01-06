import template from './sw-cms-preview-gisl-blog-category.html.twig';

Shopware.Component.register('sw-cms-preview-gisl-blog-category', {
    template,
    computed: {
        assetFilter() {
            return Shopware.Filter.getByName('asset');
        },
    }
});