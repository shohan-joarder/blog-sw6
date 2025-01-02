import './component';
import './config';

Shopware.Service('cmsService').registerCmsElement({
    name: 'gisl-blog-listing',
    label: 'Blog Listing Element',
    component: 'sw-cms-el-gisl-blog-listing',
    configComponent: 'sw-cms-el-config-gisl-blog-listing',
    defaultConfig: {
        // blogs: {
        //     source: 'static',
        //     value: []
        // },
        paginationCount: {
            source: 'static',
            value: 5,
        }
    }
});