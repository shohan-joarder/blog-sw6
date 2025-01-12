import './component';
import './config';

Shopware.Service('cmsService').registerCmsElement({
    name: 'gisl-blog-details',
    label: 'Blog Details Element',
    component: 'sw-cms-el-gisl-blog-details',
    configComponent: 'sw-cms-el-config-gisl-blog-details',
    defaultConfig: {
        showCategory: {
            source: 'static',
            value: true,
        },
        showTag: {
            source: 'static',
            value: true,
        },
        showShare: {
            source: 'static',
            value: true,
        }
    }
});