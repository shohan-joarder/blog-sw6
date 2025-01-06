import template from './sw-cms-el-gisl-blog-category.html.twig';


Shopware.Component.register('sw-cms-el-gisl-blog-category', {
    template,
    computed: {
        assetFilter() {
            return Shopware.Filter.getByName('asset');
        },
    }
});