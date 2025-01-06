import template from './sw-cms-el-gisl-blog-description.html.twig';


Shopware.Component.register('sw-cms-el-gisl-blog-description', {
    template,
    computed: {
        assetFilter() {
            return Shopware.Filter.getByName('asset');
        },
    }
});