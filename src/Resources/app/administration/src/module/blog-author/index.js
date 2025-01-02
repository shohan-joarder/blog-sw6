import './pages/blog-author-list';
import './pages/blog-author-create';

import deDE from './../../snippet/de-DE.json';
import enGB from './../../snippet/en-GB.json';

Shopware.Module.register('blog-author', {
    type: 'plugin',
    name: 'blog-author',
    title: 'gisl.author.title',
    description: 'blog-author.general.description',
    color: '#ff3d58',
    icon: 'regular-content',
    entity: 'gisl_blog_author',
    snippets: {
        'de-DE': deDE,
        'en-GB': enGB
    },
    routes: {
        list: {
            component: 'blog-author-list',
            path: 'list'
        },
        create: {
            component: 'blog-author-create',
            path: 'create/:id?',
            meta: {
                parentPath: 'blog.author.list'
            }
        }
    }
});
