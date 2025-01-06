import template from './sw-cms-el-gisl-recent-blog.html.twig';
import './sw-cms-el-gisl-recent-blog.scss';

Shopware.Component.register('sw-cms-el-gisl-recent-blog', {
    template,
    computed: {
        assetFilter() {
            return Shopware.Filter.getByName('asset');
        },
    }
});