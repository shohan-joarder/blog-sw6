import './page/blog-category-list';
import './page/blog-category-create';

import deDE from './../../snippet/de-DE.json';
import enGB from './../../snippet/en-GB.json';

Shopware.Module.register('blog-category', {
    type: 'plugin',
    name: 'blog-category',
    title: 'gisl.category.title',
    description: 'gisl.category.description',
    color: '#ff3d58',
    icon: 'regular-content',
    entity: 'gisl_blog_category',
    snippets: {
        'de-DE': deDE,
        'en-GB': enGB
    },
    routes: {
        list: {
            component: 'blog-category-list',
            path: 'list'
        },
        create: {
            component: 'blog-category-create',
            path: 'create/:id?',
            meta: {
                parentPath: 'blog.category.list'
            }
        }
    }
});
