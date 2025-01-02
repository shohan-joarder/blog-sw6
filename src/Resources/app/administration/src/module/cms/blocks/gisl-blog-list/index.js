import './component';
import './preview';

Shopware.Service('cmsService').registerCmsBlock({
    name: 'gisl-blog-listing',
    label: 'gisl.general.cms.blogTitle',
    category: 'text',
    component: 'sw-cms-block-gisl-blog-listing',
    previewComponent: 'sw-cms-preview-gisl-blog-listing',
    defaultConfig: {
        marginBottom: '20px',
        marginTop: '20px',
        marginLeft: '20px',
        marginRight: '20px',
        sizingMode: 'boxed',
    },
    slots: {
        content: {
            type: 'gisl-blog-listing',
        },
    },
});
