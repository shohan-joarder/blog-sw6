import './component';
import './preview';

Shopware.Service('cmsService').registerCmsBlock({
    name: 'gisl-related-blog',
    label: 'gisl.general.cms.relatedBlog',
    category: 'gisl-blog',
    component: 'sw-cms-block-gisl-related-blog',
    previewComponent: 'sw-cms-preview-gisl-related-blog',
    defaultConfig: {
        marginBottom: '20px',
        marginTop: '20px',
        marginLeft: '20px',
        marginRight: '20px',
        sizingMode: 'boxed',
    },
    slots: {
        content: 'gisl-related-blog',
    }
});
