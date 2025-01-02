import template from './sw-cms-el-config-gisl-blog-listing.html.twig';

const { Component, Mixin } = Shopware;

Component.register('sw-cms-el-config-gisl-blog-listing', {
    
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
            this.initElementConfig('gisl-blog-listing');
        }
    }
});