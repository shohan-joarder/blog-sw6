import template from  "./blog-author-create.html.twig"
// import "./../../../component/languages"

const { Component, Mixin } = Shopware;

const { Criteria } = Shopware.Data;

Component.register('blog-author-create', {
    
    template,

    mixins: [
        Mixin.getByName('notification'),
        Mixin.getByName('placeholder')
    ],
    inject: ['repositoryFactory','acl'],

    data() {
        return {
            item: {
                id: null,
                name: '',
                description: '',
                active: true,
                mediaId:null
            },
            errors:{
                name:null
            },
            toastTitle:"",
            toastMessage:"",
            repository:"gisl_blog_author",
            loading:false,

        };
    },
    methods: {
        async loadItem() {

            // Retrieve the item using the repository and Criteria
            const itemId = this.item.id;
            const criteria = new Criteria();
            criteria.setIds([itemId]);

            const repository = this.itemRepository;
            
            repository.search(criteria).then((result) => {
                this.item = result[0];
            });

        },
        async onSave() {
            this.loading = true;
            const isValid = await this.validateRequiredFields();
            if (!isValid) {
                this.loading = false;
                return; // Exit if validation fails
            }
            // Update the item using repository
            await this.updateItem();
        },
        async updateItem() {
            const repository = this.itemRepository;
            const isUpdating = !!this.item.id;
            const itemToSave = isUpdating ? await repository.get(this.item.id) : repository.create();
        
            // Set item properties
            itemToSave.name = this.item.name;
            itemToSave.description = this.item.description;
            itemToSave.active = this.item.active;
            itemToSave.mediaId = this.item.mediaId;
        
            try {
                // Attempt to save the item
                const result = await repository.save(itemToSave, Shopware.Context.api);
        
                // Show success notification and redirect
                this.createNotificationSuccess({
                    title:  this.$tc('gisl.general.success'),
                    message: isUpdating ? this.$tc('gisl.author.updateMessage') :  this.$tc('gisl.author.saveMessage')
                });
        
                // Redirect to list page after success notification
                this.$router.push({ name: 'blog.author.list' });
        
            } catch (error) {
        
                // Show error notification if an error occurs
                this.createNotificationError({
                    title: this.$tc('gisl.general.error'),
                    message: this.$tc('gisl.general.common_error_message'),
                });
            } finally {
                this.loading = false;
            }
        },
        async validateRequiredFields() {
            let isValid = true;

            // Check if each required field is not empty
            if (!this.item.name) {
                this.errors.name = "Name is required";
                isValid = false;
            } else {
                this.errors.name = null;
            }

            return isValid;
        },
        checkIfId(){
            const itemId = this.$route.params.id

            if(itemId){
                
                this.item.id = itemId;
    
                this.loadItem();
    
            }
        }
    },
    computed: {

        itemRepository() {
            return this.repositoryFactory.create(this.repository);
        },

        cardTitle() {
            return this.item.id 
            ? `${this.$tc('gisl.general.update')} ${this.$tc('gisl.author.title')}` 
            : `${this.$tc('gisl.general.save')} ${this.$tc('gisl.author.title')}`;
        },
    },
    mounted() {

       this.checkIfId();

    }
});