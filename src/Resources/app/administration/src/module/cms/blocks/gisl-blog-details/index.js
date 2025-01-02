import './component';
import './preview';

Shopware.Service('cmsService').registerCmsBlock({
    name: 'gisl-blog-details',
    label: 'gisl.general.cms.blogDetailsTitle',
    category: 'text',
    component: 'sw-cms-block-gisl-blog-details',
    previewComponent: 'sw-cms-preview-gisl-blog-details',
    defaultConfig: {
        marginBottom: '20px',
        marginTop: '20px',
        marginLeft: '20px',
        marginRight: '20px',
        sizingMode: 'boxed',
    },
    slots: {
        content: {
            type: 'gisl-blog-details',
        },
    },
});
