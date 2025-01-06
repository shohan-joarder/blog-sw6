import './component';
import './preview';

Shopware.Service('cmsService').registerCmsBlock({
    name: 'gisl-blog-description',
    label: 'gisl.general.cms.blogDescription',
    category: 'gisl-blog',
    component: 'sw-cms-block-gisl-blog-description',
    previewComponent: 'sw-cms-preview-gisl-blog-description',
    defaultConfig: {
        marginBottom: '20px',
        marginTop: '20px',
        marginLeft: '20px',
        marginRight: '20px',
        sizingMode: 'boxed',
    },
    slots: {
        content: {
            type: 'gisl-blog-description',
        },
    }
});
