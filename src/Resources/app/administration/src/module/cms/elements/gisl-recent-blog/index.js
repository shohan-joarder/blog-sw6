import './component';
import './config';

Shopware.Service('cmsService').registerCmsElement({
    name: 'gisl-recent-blog',
    label: 'Blog Recent Blog Element',
    component: 'sw-cms-el-gisl-recent-blog',
    configComponent: 'sw-cms-el-config-gisl-recent-blog',
    defaultConfig: {
        sectionTitle: {
            source: 'static',
            value: "Recent Blog",
        }
    }
});