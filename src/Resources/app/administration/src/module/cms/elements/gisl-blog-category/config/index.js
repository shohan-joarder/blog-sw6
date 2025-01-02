import template from './sw-cms-el-config-gisl-blog-category.html.twig';

const { Component, Mixin } = Shopware;

Shopware.Component.register('sw-cms-el-config-gisl-blog-category', {
    template,

    inject: ['repositoryFactory'],

    mixins: [
        Mixin.getByName('cms-element'),
    ],

    created() {
        this.createdComponent();
    },

    methods: {
        async createdComponent() {
            this.initElementConfig('gisl-blog-category');
        }
    }
});