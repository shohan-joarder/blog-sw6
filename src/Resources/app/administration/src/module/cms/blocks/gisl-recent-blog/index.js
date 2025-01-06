import './component';
import './preview';

Shopware.Service('cmsService').registerCmsBlock({
    name: 'gisl-recent-blog',
    label: 'gisl.general.cms.recentBlog',
    category: 'gisl-blog',
    component: 'sw-cms-block-gisl-recent-blog',
    previewComponent: 'sw-cms-preview-gisl-recent-blog',
    defaultConfig: {
        marginBottom: '20px',
        marginTop: '20px',
        marginLeft: '20px',
        marginRight: '20px',
        sizingMode: 'boxed',
    },
    slots: {
        content: 'gisl-recent-blog',
    }
});
