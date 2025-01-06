import './component';
import './config';

Shopware.Service('cmsService').registerCmsElement({
    name: 'gisl-related-blog',
    label: 'Blog Related Blog Element',
    component: 'sw-cms-el-gisl-related-blog',
    configComponent: 'sw-cms-el-config-gisl-related-blog',
    defaultConfig: {
        sectionTitle: {
            source: 'static',
            value: "Related Blog",
        }
    }
});