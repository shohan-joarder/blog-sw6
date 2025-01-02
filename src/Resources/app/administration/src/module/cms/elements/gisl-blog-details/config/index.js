import template from './sw-cms-el-config-gisl-blog-details.html.twig';

const { Component, Mixin } = Shopware;

Component.register('sw-cms-el-config-gisl-blog-details', {
    
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
            this.initElementConfig('gisl-blog-details');
        }
    }
});