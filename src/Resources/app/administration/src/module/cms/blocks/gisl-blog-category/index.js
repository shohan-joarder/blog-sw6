import './component';
import './preview';

Shopware.Service('cmsService').registerCmsBlock({
    name: 'gisl-blog-category',
    label: 'gisl.general.cms.blogCategoryTitle',
    category: 'text',
    component: 'sw-cms-block-gisl-blog-category',
    previewComponent: 'sw-cms-preview-gisl-blog-category',
    defaultConfig: {
        marginBottom: '20px',
        marginTop: '20px',
        marginLeft: '20px',
        marginRight: '20px',
        sizingMode: 'boxed',
    },
    slots: {
        content: {
            type: 'gisl-blog-category',
        },
    },
});
