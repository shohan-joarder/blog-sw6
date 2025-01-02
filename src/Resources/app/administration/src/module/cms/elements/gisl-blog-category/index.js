import './component';
import './config';

Shopware.Service('cmsService').registerCmsElement({
    name: 'gisl-blog-category',
    label: 'Blog Listing Element',
    component: 'sw-cms-el-gisl-blog-category',
    configComponent: 'sw-cms-el-config-gisl-blog-category',
    defaultConfig: {
        sectionTitle: {
            source: 'static',
            value: "Category"
        }
    },
});