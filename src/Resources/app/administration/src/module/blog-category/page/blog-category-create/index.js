import template from  "./blog-category-create.html.twig"
import "./../../../component/languages"
import "./../../../gisl_blog.scss"
const { Component, Mixin } = Shopware;

const { Criteria } = Shopware.Data;

Component.register('blog-category-create', {
    
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
                slug: '',
                description: '',
                shortDescription: '',
                active: true,
                mediaId:null,
                meta_title:"",
                meta_description:"",
                meta_keywords:"",
            },
            errors:{
                name:null,
                slug:null,
                short_description:null
            },
            languageId:null,
            languageIdPk:null,
            toastTitle:"",
            toastMessage:"",
            repository:"gisl_blog_category",
            loading:false
        };
    },
    methods: {
        async loadItem() {
            try {
                const itemId = this.item?.id; // Safely access item ID
                if (!itemId) {
                    console.warn("Item ID is not available.");
                    return;
                }
        
                const repository = this.itemRepository;
                if (!repository) {
                    throw new Error("Item repository is not available.");
                }
        
                // Load the main item
                const criteria = new Criteria();
                criteria.setIds([itemId]);
        
                const result = await repository.search(criteria, Shopware.Context.api);
        
                if (result && result.length > 0) {
                    const category = result[0];
                    this.item = { 
                        // Update the item safely with spread operator
                        ...this.item,
                        id: category.id,
                        active: category.active,
                        mediaId: category.mediaId,
                    };
                } else {
                    console.warn("Item not found with ID:", itemId);
                    return;
                }

                const langData =await Shopware.Utils.GlobalFunctions.getActiveLangData('category', itemId, this.languageId, false);

                if(langData != null){
                    this.item = {
                        ...this.item,
                        name: langData.title || "",
                        slug: langData.slug || "",
                        description: langData.description || "",
                        shortDescription: langData.shortDescription || "",
                        meta_title:langData.meta_title || "",
                        meta_description:langData.meta_description || "",
                        meta_keywords:langData.meta_keywords || ""
                    };
                    this.languageIdPk = langData.id || null;
                }else{
                    this.item = {
                        ...this.item,
                        name: "",
                        slug: "",
                        description: "",
                        shortDescription: "",
                        meta_title:"",
                        meta_description:"",
                        meta_keywords:""
                    };
                    this.languageIdPk = null;
                }

            } catch (error) {
                this.createNotificationError({
                    title: this.$tc("gisl.general.error"),
                    message: this.$tc("gisl.general.common_error_message")
                });
            }
        },        
        async onSave() {

            const isValid = await this.validateRequiredFields();
            if (!isValid) {
                this.loading = false;
                return; // Exit if validation fails
            }

            this.loading = true;

            const isUniqueSlug =  await Shopware.Utils.GlobalFunctions.validateUniqueSlug(this.item.slug,this.languageIdPk);
            
            const {error:errorMessage} = isUniqueSlug

            this.loading = false;
            
            if (errorMessage != null) {
                console.log("Error msg",errorMessage)
                this.createNotificationError({
                    title: "Duplicate Slug",
                    message:errorMessage,
                });
                return;
            }
            // Update the item using repository
            await this.updateItem();
        },
        async updateItem() {

            const repository = this.itemRepository;
            const isUpdating = !!this.item.id;
            const itemToSave = isUpdating ? await repository.get(this.item.id) : repository.create();
        
            itemToSave.active = this.item.active;
            itemToSave.mediaId = this.item.mediaId;
        
            try {
                // Save the item; will update if ID exists, otherwise create new
                await repository.save(itemToSave, Shopware.Context.api);
        
                const savedEntity = await repository.get(itemToSave.id, Shopware.Context.api);

                const savedId = savedEntity.id;

                const langData = {
                    type:"category",
                    languageIdPk:this.languageIdPk,
                    langId:this.languageId,
                    title:this.item.name,
                    shortDescription:this.item.shortDescription,
                    description:this.item.description,
                    slug: this.item.slug,
                    meta_title: this.item.meta_title,
                    meta_description: this.item.meta_description,
                    meta_keywords: this.item.meta_keywords
                }

                
                await Shopware.Utils.GlobalFunctions.storeLanguageData(savedId,langData);
                // this.storeLanguageData(savedId);

                // Display success notification
                this.createNotificationSuccess({
                    title: this.$tc("gisl.general.success"),
                    message: isUpdating 
                    ? this.$tc('gisl.category.updateMessage') :  this.$tc('gisl.category.saveMessage')
                });
        
                // Redirect to category list
                this.$router.push({ name: 'blog.category.list' });
        
            } catch (error) {
                // Display error notification
                this.createNotificationError({
                    title: this.$tc("gisl.general.error"),
                    message:this.$tc("gisl.general.common_error_message"),
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

            if (!this.item.slug) {
                this.errors.slug = "Slug is required";
                isValid = false;
            } else {
                this.errors.slug = null;
            }

            if (!this.item.shortDescription) {
                this.errors.short_description = "Short description is required";
                isValid = false;
            } else {
                this.errors.short_description = null;
            }

            return isValid;
        },

        appLanguage(){
            const languageId = Shopware.Context.api.systemLanguageId;  //Shopware.Context.api.languageId; // Current language ID
            this.languageId = languageId
        },
        handleLanguageChange(newLanguageId) {

            this.languageId = newLanguageId;  // Update the languageId in the parent

            this.loadItem();

        }
    },

    watch: {
        'item.name': function(newName, oldName) {
            if (oldName && newName !== oldName) {
                this.item.slug = Shopware.Utils.GlobalFunctions.generateSlug(this.item.name);
            }
        }
    },
    computed: {

        itemRepository() {
            return this.repositoryFactory.create(this.repository);
        },

        cardTitle() {
            return this.item.id 
            ? `${this.$tc('gisl.general.update')} ${this.$tc('gisl.category.title')}` 
            : `${this.$tc('gisl.general.save')} ${this.$tc('gisl.category.title')}`;
        },
    },
    created() {
        // Watch for Shopware language context changes
        // this.trackLanguageContextChange();
    },
    mounted() {

        this.languageId = Shopware.Utils.GlobalFunctions.appLanguage();

        const itemId = this.$route.params.id

        if(itemId){
            
            this.item.id = itemId;

            this.loadItem();

        }

    }

});