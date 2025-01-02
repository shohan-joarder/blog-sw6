import template from  "./blog-category-list.html.twig"
import "./../../../component/blog-aside"
import "./../../../component/languages"
import "./../../../gisl_blog.scss"

const { Component, Mixin } = Shopware;

const { Criteria } = Shopware.Data;

Component.register('blog-category-list', {
    
    template,

    mixins: [
        Mixin.getByName('notification')
    ],

    inject: ['repositoryFactory'],
    data() {
        return {
            items: [],
            total: 0,
            limit: 10,
            page: 1,
            sortBy: 'createdAt',
            sortDirection: 'DESC',
            naturalSorting: false,
            columns: [
                { property: 'name', label: 'Title',routerLink: 'blog.category.create', },
                { property: 'slug', label: 'Slug' },
                { property: 'meta_title', label: 'Meta Title' }
            ],
            languageId:null,
            entity:"gisl_blog_category",
            categoryId:null,
            confirmModal:false
        };
    },
    methods: {
        loadItems() {

            const criteria = new Criteria(this.page, this.limit);
            criteria.addSorting(Criteria.sort(this.sortBy, this.sortDirection, this.naturalSorting));
            criteria.addAssociation('media');

            this.repository = this.repositoryFactory.create(this.entity);
            this.repository.search(criteria, Shopware.Context.api).then(async (result) => {
                if (result && result.length > 0) {
                    // Clear the existing items array to avoid duplication
                    this.items = [];
            
                    // Iterate over the results
                    for (const item of result) {
                        const langData = await Shopware.Utils.GlobalFunctions.getActiveLangData('category', item.id, this.languageId, true);
                        // Safeguard against missing langData
                        this.items.push({
                            id:item.id || null,
                            name: langData?.title || '',
                            slug: langData?.slug || '',
                            meta_title: langData?.meta_title || '',
                        });
                    }
                }
            
                // Assign the total
                this.total = result.total || 0;
            });

        },
        onEditItem(item){
            const id = item.id;
            this.$router.push({ name: 'blog.category.create', params: {id}  });
        },
        onPageChange(newPage) {
            const {page,limit} = newPage;
            this.limit = limit;
            this.page = page;
            this.loadItems();
        },
        async onDeleteItem() {
            let itemId = this.categoryId;
        
            if (!itemId) {
                this.createNotificationError({
                    title: this.$tc("gisl.general.error"),
                    message: this.$tc("gisl.general.common_error_message")
                });
                return;
            }
        
            try {
                const repository = this.itemRepository;
                const languageRepository = this.repositoryFactory.create('gisl_blog_translation');
        
                if (!repository || !languageRepository) {
                    this.createNotificationError({
                        title: this.$tc("gisl.general.error"),
                        message: this.$tc("gisl.general.common_error_message")
                    });
                    return;
                }
        
                // Delete associated language/translation data first
                const criteria = new Shopware.Data.Criteria();
                criteria.addFilter(Shopware.Data.Criteria.equals('fkId', itemId));
        
                const translations = await languageRepository.search(criteria, Shopware.Context.api);
        
                for (const translation of translations) {
                    await languageRepository.delete(translation.id, Shopware.Context.api);
                }
        
                // Now delete the main item
                await repository.delete(itemId, Shopware.Context.api);
        
                this.confirmModal = false;
        
                // Notify success
                this.createNotificationSuccess({
                    title: this.$tc("gisl.general.success"),
                    message: this.$tc("gisl.general.common_delete_message")
                });
        
                // Refresh the list after deletion
                this.loadItems();
            } catch (error) {
                // Display error notification
                this.createNotificationError({
                    title: this.$tc("gisl.general.error"),
                    message: this.$tc("gisl.general.common_error_message")
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
    computed:{
        itemRepository() {
            return this.repositoryFactory.create(this.entity);
        }
    },
    mounted() {

        this.languageId = Shopware.Utils.GlobalFunctions.appLanguage();

    }
    
})
