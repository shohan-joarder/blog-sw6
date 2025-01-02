import './component';
import './preview';

Shopware.Service('cmsService').registerCmsBlock({
    name: 'gisl-blog-short-description',
    label: 'gisl.general.cms.blogShortDescription',
    category: 'text',
    component: 'sw-cms-block-gisl-blog-short-description',
    previewComponent: 'sw-cms-preview-gisl-blog-short-description',
    defaultConfig: {
        marginBottom: '20px',
        marginTop: '20px',
        marginLeft: '20px',
        marginRight: '20px',
        sizingMode: 'boxed',
    },
    slots: {
        content: {
            type: 'gisl-blog-short-description',
        },
    }
});
