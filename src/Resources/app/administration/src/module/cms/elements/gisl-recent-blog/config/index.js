import template from './sw-cms-el-config-gisl-recent-blog.html.twig';
import "./sw-cms-el-config-gisl-recent-blog.scss"

const { Component, Mixin } = Shopware;

Component.register('sw-cms-el-config-gisl-recent-blog', {
    
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
            this.initElementConfig('gisl-recent-blog');
        }
    }
});