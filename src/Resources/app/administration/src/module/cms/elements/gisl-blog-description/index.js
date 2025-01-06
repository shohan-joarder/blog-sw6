import './component';
import './config';

Shopware.Service('cmsService').registerCmsElement({
    name: 'gisl-blog-description',
    label: 'Blog Description Element',
    component: 'sw-cms-el-gisl-blog-description',
    configComponent: 'sw-cms-el-config-gisl-blog-description',
    defaultConfig: {
        targetId: {
            source: 'static',
            value: "gislBlogDescription"
        },
        title:{
            source: 'static',
            value: "Blog"
        }
    },
});