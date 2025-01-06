import template from  "./blog-post-create.html.twig"

import "./../../../component/quill-editor"
import "./blog-post-create.scss"


const { Component, Mixin,Context,Application } = Shopware;

const { Criteria } = Shopware.Data;

import deDE from './../../../../snippet/de-DE.json';
import enGB from './../../../../snippet/en-GB.json';

Component.register('blog-post-create', {
    
    template,

    mixins: [
        Mixin.getByName('notification'),
        Mixin.getByName('placeholder')
    ],
    snippets: {
        'de-DE': deDE,
        'en-GB': enGB
    },
    inject: ['repositoryFactory','acl'],

    data() {
        return {
            item: {
                id: null,
                title: '',
                slug: '',
                shortDescription: '',
                description: '',
                publishedAt:'',
                active: true,
                mediaId:null,
                authorId:null,
                categories:[],
                meta_title:"",
                meta_description:"",
                meta_keywords:"",
                tags:[]
            },
            errors:{
                title:null,
                slug:null,
                short_description:null,
                categories:null,
                authorId:null,
                publishedAt:null,
            },
            languageId:null,
            languageIdPk:null,
            loading:false,
            createdId:null,
            entity:"gisl_blog_post",
            authors:[],
            categoryList:[],
            tags:[],
            showMediaModal: false,
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
                    const item = result[0];

                    console.log(item)
                    this.item = { 
                        // Update the item safely with spread operator
                        ...this.item,
                        ...item,
                    };

                    if(item.tags == null){
                        this.item = {
                            ...this.item,
                            tags:[]
                        }
                    }
                } else {
                    console.warn("Item not found with ID:", itemId);
                    return;
                }

                const langData = await Shopware.Utils.GlobalFunctions.getActiveLangData('blog', itemId, this.languageId, false);

                if(langData != null){
                    this.item = {
                        ...this.item,
                        title: langData.title || "",
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
                        title: "",
                        slug: "",
                        description: "",
                        shortDescription: "",
                        meta_title:"",
                        meta_description:"",
                        meta_keywords:""
                    };
                    this.languageIdPk = null;
                }

                console.log(this.item)

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
        
            const replacements = {
                ':': '[CLONE]',
                ',': '[COMMA]',
                ';': '[SEMICLONE]'
            };

            this.item.description = this.item.description
                                    .replace(/<iframe/g, '[iframe]')
                                    .replace(/<\/iframe>/g, '[/iframe]');

            itemToSave.active = this.item.active;
            itemToSave.publishedAt = this.item.publishedAt;
            itemToSave.mediaId = this.item.mediaId;
            itemToSave.authorId = this.item.authorId;
            itemToSave.tags = this.item.tags;
            itemToSave.categories = this.item.categories;
        
            // Extract and assign tag names
            if (this.item.tags) {
                itemToSave.tags_name = this.tags
                    .filter(tag => this.item.tags.includes(tag.id))
                    .map(tag => tag.name);
            } else {
                itemToSave.tags_name = []; // Set to empty array if tags are undefined
            }
            
            try {
                // Save the item; will update if ID exists, otherwise create new
                await repository.save(itemToSave, Shopware.Context.api);
        
                const savedEntity = await repository.get(itemToSave.id, Shopware.Context.api);

                const savedId = savedEntity.id;

                const description =  this.item.description.replace(
                    /src="([^"]*)"/g, // Matches the src="..." pattern
                    (match, group) => {
                        const replacedValue = group.replace(/[:,;]/g, (innerMatch) => replacements[innerMatch]);
                        return `src="${replacedValue}"`; // Replace only the src content
                    }
                );


                const langData = {
                    type:"blog",
                    languageIdPk:this.languageIdPk,
                    langId:this.languageId,
                    title:this.item.title,
                    shortDescription:this.item.shortDescription,
                    description:description,
                    slug: this.item.slug,
                    meta_title: this.item.meta_title,
                    meta_description: this.item.meta_description,
                    meta_keywords: this.item.meta_keywords
                }
                
                await Shopware.Utils.GlobalFunctions.storeLanguageData(savedId,langData);

                // Display success notification
                this.createNotificationSuccess({
                    title: this.$tc("gisl.general.success"),
                    message: isUpdating 
                    ? this.$tc('gisl.blog.updateMessage') :  this.$tc('gisl.blog.saveMessage')
                });
        
                // Redirect to category list
                this.$router.push({ name: 'blog.post.list' });
        
            } catch (error) {
                // Display error notification
                this.createNotificationError({
                    title: this.$tc("gisl.general.error"),
                    message:this.$tc("gisl.general.common_error_message"),
                });
            }finally {
                this.loading = false;
            }
        },
        checkIfId(){
            const itemId = this.$route.params.id

            if(itemId){
                
                this.item.id = itemId;
    
                this.loadItem();
    
            }
        },
        getAllAuthors() {
            
            const criteria = new Criteria(1, 500); 

            const authorRepository = this.repositoryFactory.create("gisl_blog_author");

            return authorRepository.search(criteria, Shopware.Context.api).then((authors) => {
                this.authors = authors
            }).catch((error) => {
                // console.error('Error fetching authors:', error);
            });
        },
        getAllTags() {
            
            const criteria = new Criteria(1, 500); 

            const tagRepository = this.repositoryFactory.create("tag");

            return tagRepository.search(criteria, Shopware.Context.api).then((tags) => {
                this.tags = tags
            }).catch((error) => {
                // console.error('Error fetching authors:', error);
            });
        },
        newTag(){
            this.$router.push({ name: 'sw.settings.tag.index'});
        },
        async getAllCategories() {

            const criteria = new Criteria(1, 500); 

            const categoryRepository = this.repositoryFactory.create("gisl_blog_category");

            categoryRepository.search(criteria, Shopware.Context.api).then(async(result) => {

                if (result && result.length > 0) {
                    // Clear the existing items array to avoid duplication
                    this.categoryList = [];
            
                    // Iterate over the results
                    for (const item of result) {
                        const langData = await Shopware.Utils.GlobalFunctions.getActiveLangData('category', item.id, this.languageId, true);
                        // Safeguard against missing langData
                        this.categoryList.push({
                            id:item.id || null,
                            name: langData?.title || '',
                        });
                    }
                }

            })
        },
        async validateRequiredFields() {
            let isValid = true;

            // Check if each required field is not empty
            if (!this.item.title) {
                this.errors.title = "Title is required";
                isValid = false;
            } else {
                this.errors.title = null;
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
          
            if (!this.item.authorId) {
                this.errors.authorId = "Author is required";
                isValid = false;
            } else {
                this.errors.slug = null;
            }

            if (!this.item.publishedAt) {
                this.errors.publishedAt = "Published date is required";
                isValid = false;
            } else {
                this.errors.publishedAt = null;
            }

            return isValid;
        },
        async validateUniqueSlug() {
            const criteria = new Shopware.Data.Criteria();
            criteria.addFilter(Shopware.Data.Criteria.equals('slug', this.item.slug));
        
            // Exclude the current item if it's an update operation
            if (this.item.id) {
                // Correct the syntax for excluding the current item by id
                criteria.addFilter(
                    Shopware.Data.Criteria.not('AND', [
                        Shopware.Data.Criteria.equals('id', this.item.id)
                    ])
                );
            }
        
            const repository = this.itemRepository;

            const result = await repository.search(criteria, Shopware.Context.api);
        
            if (result.total > 0) {
                // Set an error if a duplicate slug is found
                this.errors.slug = "The slug you entered already exists";
            } else {
                this.errors.slug = null; // Clear the error if no duplicate is found
            }
        
            this.loading = false;
        
            return result.total === 0; // Return true if no duplicates are found
        },
        updateDescription(content) {
            this.item.description = content;
        },
        handleLanguageChange(newLanguageId) {

            this.languageId = newLanguageId;  // Update the languageId in the parent

            this.loadItem();

        }
    },
    watch: {
        'item.title': function(newName, oldName) {
            if (oldName && newName !== oldName) {
                this.item.slug = Shopware.Utils.GlobalFunctions.generateSlug(this.item.title);
            }
        }
    },
    computed: {
        itemRepository() {
            return this.repositoryFactory.create(this.entity);
        },
        cardTitle() {
            return this.item.id 
            ? `${this.$tc('gisl.general.update')} ${this.$tc('gisl.blog.title')}` 
            : `${this.$tc('gisl.general.save')} ${this.$tc('gisl.blog.title')}`;
        },
    
    },
    mounted() {

        this.languageId = Shopware.Utils.GlobalFunctions.appLanguage();

        this.checkIfId();

        this.getAllAuthors();

        this.getAllCategories();

        this.getAllTags();
        
    }
});