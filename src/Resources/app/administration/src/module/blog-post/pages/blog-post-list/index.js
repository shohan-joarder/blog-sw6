import template from  "./blog-post-list.html.twig"
import "./../../../component/blog-aside"
import "./../../../gisl_blog.scss"

import deDE from './../../../../snippet/de-DE.json';
import enGB from './../../../../snippet/en-GB.json';

const { Component, Mixin } = Shopware;

const { Criteria } = Shopware.Data;

Component.register('blog-post-list', {
    
    template,

    mixins: [
        Mixin.getByName('notification')
    ],
    snippets: {
        'de-DE': deDE,
        'en-GB': enGB
    },
    inject: ['repositoryFactory'],
    data() {
        return {
            postId:null,
            items: [],
            total: 0,
            limit: 10,
            page: 1,
            sortBy: 'createdAt',
            sortDirection: 'DESC',
            naturalSorting: false,
            columns: [
                { property: 'title', label: 'Title',routerLink: 'blog.post.create', width: '200px' },
                // { property: 'slug', label: 'Slug', width: '150px' },
                { property: 'author', label: 'Author', width: '100px' },
                // { property: 'meta_title', label: 'Meta Title', width: '150px' }
            ],
            entity:"gisl_blog_post",
            confirmModal:false,
            languageId:null
        };
    },
    methods: {
        async loadItems() {

            const criteria = new Criteria(this.page, this.limit);
            criteria.addSorting(Criteria.sort(this.sortBy, this.sortDirection, this.naturalSorting));
            criteria.addAssociation('postAuthor');

            this.repository = this.repositoryFactory.create(this.entity);

            this.repository.search(criteria, Shopware.Context.api).then(async (result) => {

                if (result && result.length > 0) {
                    // Clear the existing items array to avoid duplication
                    this.items = [];
            
                    // Iterate over the results
                    for (const item of result) {
                        const langData = await Shopware.Utils.GlobalFunctions.getActiveLangData('blog', item.id, this.languageId, true);
                        // Safeguard against missing langData
                        this.items.push({
                            id:item.id || null,
                            title: langData?.title || '',
                            author: item?.postAuthor?.name || '',
                            slug: langData?.slug || '',
                            meta_title: langData?.meta_title || '',
                        });
                    }
                }

            });

        },
        onEditItem(item){
            const id = item.id;
            this.$router.push({ name: 'blog.post.create', params: {id}  });
        },
        onPageChange(newPage) {
            const {page,limit} = newPage;
            this.limit = limit;
            this.page = page;
            this.loadItems();
        },
        async onDeleteItem() {
            const itemId = this.postId;
        
            if (!itemId) {
                this.createNotificationError({
                    title: this.$tc("gisl.general.error"),
                    message: this.$tc("gisl.general.common_error_message"),
                });
                return;
            }
        
            try {
                const repository = this.itemRepository;
                
                const languageRepository = this.repositoryFactory.create('gisl_blog_translation');
        
                if (!repository || !languageRepository) {
                    this.createNotificationError({
                        title: this.$tc("gisl.general.error"),
                        message: this.$tc("gisl.general.common_error_message"),
                    });
                    return;
                }
        
                // Delete associated language/translation data first
                const criteria = new Shopware.Data.Criteria();
                criteria.addFilter(Shopware.Data.Criteria.equals('fkId', itemId));
        
                const translations = await languageRepository.search(criteria, Shopware.Context.api);
        
                if (translations.total > 0) {
                    // Use `Promise.all` for efficient deletion of translations
                    await Promise.all(
                        translations.map((translation) =>
                            languageRepository.delete(translation.id, Shopware.Context.api)
                        )
                    );
                }
        
                // Delete the main item
                await repository.delete(itemId, Shopware.Context.api);
        
                this.confirmModal = false;
        
                // Notify success
                this.createNotificationSuccess({
                    title: this.$tc("gisl.general.success"),
                    message: this.$tc("gisl.general.common_delete_message"),
                });
        
                // Refresh the list after deletion
                this.loadItems();
            } catch (error) {
                console.error("Error deleting item:", error);
        
                // Display error notification
                this.createNotificationError({
                    title: this.$tc("gisl.general.error"),
                    message: this.$tc("gisl.general.common_error_message"),
                });
            }
        },
        
        handleLanguageChange(newLanguageId) {

            this.languageId = newLanguageId;  // Update the languageId in the parent

            this.loadItems();

        }
    },
    
    created() {
        this.loadItems();
    },
    
    itemRepository() {
        return this.repositoryFactory.create(this.entity);
    },

    mounted() {

        this.languageId = Shopware.Utils.GlobalFunctions.appLanguage();

    }
})
