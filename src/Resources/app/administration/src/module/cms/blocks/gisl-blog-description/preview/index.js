import template from './sw-cms-preview-gisl-blog-description.html.twig';

Shopware.Component.register('sw-cms-preview-gisl-blog-description', {
    template,
    computed: {
        assetFilter() {
            return Shopware.Filter.getByName('asset');
        },
    }
});