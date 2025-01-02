import template from  "./blog-author-list.html.twig"
// import "./../../../component/languages"
import "./../../../component/blog-aside"
import "./../../../gisl_blog.scss"

const { Component, Mixin } = Shopware;

const { Criteria } = Shopware.Data;

Component.register('blog-author-list', {
    
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
                { property: 'name', label: 'Title',routerLink: 'blog.author.create', },
                { property: 'description', label: 'Description' }
            ],
            entity:"gisl_blog_author",
            itemId:null,
            confirmModal:false
        };
    },
    methods: {
        loadItems() {

            const criteria = new Criteria(this.page, this.limit);
            criteria.addSorting(Criteria.sort(this.sortBy, this.sortDirection, this.naturalSorting));
            
            this.repository = this.repositoryFactory.create(this.entity);
            this.repository.search(criteria).then((result) => {
                this.items = result;
                this.total = result.total;
            })

        },
        onEditItem(item){
            const id = item.id;
            this.$router.push({ name: 'blog.author.create', params: {id}  });
        },
        onPageChange(newPage) {
            const {page,limit} = newPage;
            this.limit = limit;
            this.page = page;
            this.loadItems();
        },
        async onDeleteItem() {
            let itemId = this.itemId
            if (!itemId) {
                console.error("Item ID is undefined or null.");
                return;
            }
    
            try {
                const repository = this.repositoryFactory.create(this.entity);
    
                // Attempt to delete the item
                await repository.delete(itemId, Shopware.Context.api);
                this.confirmModal = false;
                // Notify success
                this.createNotificationSuccess({
                    title: this.$tc('gisl.general.success'),
                    message: this.$tc('gisl.general.common_delete_message')
                });
    
                // Refresh the list after deletion
                this.loadItems();
            } catch (error) {
    
                // Display error notification
                this.createNotificationError({
                    title: this.$tc('gisl.general.error'),
                    message: this.$tc('gisl.general.common_error_message')
                });
            }
        }
    },
    created() {
        this.loadItems();
    },
    itemRepository() {
        return this.repositoryFactory.create(this.entity);
    }
})
