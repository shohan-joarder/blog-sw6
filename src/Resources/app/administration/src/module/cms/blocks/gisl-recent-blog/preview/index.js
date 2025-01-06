import template from './sw-cms-preview-gisl-recent-blog.html.twig';
import './sw-cms-preview-gisl-recent-blog.scss';

Shopware.Component.register('sw-cms-preview-gisl-recent-blog', {
    template,
    computed: {
        assetFilter() {
            return Shopware.Filter.getByName('asset');
        },
    }
});