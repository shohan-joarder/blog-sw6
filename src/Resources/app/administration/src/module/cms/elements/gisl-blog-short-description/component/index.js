import template from './sw-cms-el-gisl-blog-short-description.html.twig';


Shopware.Component.register('sw-cms-el-gisl-blog-short-description', {
    template,
    computed: {
        assetFilter() {
            return Shopware.Filter.getByName('asset');
        },
    }
});