import template from './sw-cms-el-gisl-related-blog.html.twig';
import './sw-cms-el-gisl-related-blog.scss';

Shopware.Component.register('sw-cms-el-gisl-related-blog', {
    template,
    computed: {
        assetFilter() {
            return Shopware.Filter.getByName('asset');
        },
    }
});