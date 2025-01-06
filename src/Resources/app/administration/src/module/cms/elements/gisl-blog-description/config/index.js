import template from './sw-cms-el-config-gisl-blog-description.html.twig';

const { Component, Mixin } = Shopware;
// const { EntityCollection, Criteria } = Shopware.Data;

Component.register('sw-cms-el-config-gisl-blog-description', {
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
            this.initElementConfig('gisl-blog-description');
        }
    }
});
