import template from './sw-cms-el-config-gisl-related-blog.html.twig';
import "./sw-cms-el-config-gisl-related-blog.scss"

const { Component, Mixin } = Shopware;

Component.register('sw-cms-el-config-gisl-related-blog', {
    
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
            this.initElementConfig('gisl-related-blog');
        }
    }
});