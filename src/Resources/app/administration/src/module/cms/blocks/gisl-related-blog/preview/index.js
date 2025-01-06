import template from './sw-cms-preview-gisl-related-blog.html.twig';
import './sw-cms-preview-gisl-related-blog.scss';

Shopware.Component.register('sw-cms-preview-gisl-related-blog', {
    template,
    computed: {
        assetFilter() {
            return Shopware.Filter.getByName('asset');
        },
    }
});