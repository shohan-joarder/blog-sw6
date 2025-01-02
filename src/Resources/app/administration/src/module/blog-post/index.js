import './pages/blog-post-list';
import './pages/blog-post-create';

import deDE from './../../snippet/de-DE.json';
import enGB from './../../snippet/en-GB.json';

const { Module } = Shopware;

// Register the module for your blog entity
Module.register('blog-post', {
    type: 'plugin',
    name: 'blog-post',
    title: 'gisl.blog.title', // This refers to the translation key
    description: 'gisl.blog.description', // This also refers to a translation key
    color: '#ff3d58',
    icon: 'regular-content',
    entity: 'gisl_blog_post', // Ensure this matches your entity definition

    routes: {
        list: {
            component: 'blog-post-list',
            path: 'list'
        },
        create: {
            component: 'blog-post-create',
            path: 'create/:id?',
            meta: {
                parentPath: 'blog.post.list'
            }
        }
    },

    navigation: [
        {
            id: 'blog-post-list',
            label: 'gisl.general.title',
            color: '#ff3d58',
            path: 'blog.post.list',
            parent: 'sw-content',
            position: 100
        }
    ]
});