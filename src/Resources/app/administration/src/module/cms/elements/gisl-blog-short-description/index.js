import './component';
import './config';

Shopware.Service('cmsService').registerCmsElement({
    name: 'gisl-blog-short-description',
    label: 'Blog Listing Element',
    component: 'sw-cms-el-gisl-blog-short-description',
    configComponent: 'sw-cms-el-config-gisl-blog-short-description',
    defaultConfig: {
        title: {
            source: 'static',
            value: "Blog"
        },
        buttonName: {
            source: 'static',
            value: "Read More"
        },
        targetId: {
            source: 'static',
            value: "#gislBlogDescription"
        }
    },
});