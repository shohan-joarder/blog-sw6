import template from"./languages.html.twig"

const { Component } = Shopware;

Component.register('languages', {
    template,
    props: {
        languageId: {
            type: String,
            required: true, // The parent will pass this prop
        },
        isDisable: {
            type:Boolean,
            default:false
        }
    },
    data() {
        return {
            selectedLanguage: null,
            languageOptions: [],
            isLoading: false,
        };
    },
    created() {
        this.loadLanguages();
    },

    methods: {
        async loadLanguages() {
            this.isLoading = true;
            const languageRepository = Shopware.Service('repositoryFactory').create('language');
            const criteria = new Shopware.Data.Criteria();

            try {
                const languages = await languageRepository.search(criteria, Shopware.Context.api);

                languages.map(language=>{
                    this.languageOptions.push({
                        id:language.id,
                        name:language.name
                    })
                })
                
            } catch (error) {
                this.createNotificationError({
                    title: 'Error',
                    message: 'Could not load languages.',
                });
            } finally {
                this.isLoading = false;
            }
        },
        handleLanguageChange(event) {
            const newLanguageId = event.target.value;
            // Emit the change event to notify the parent
            this.$emit('change', newLanguageId);
        },
    },
});
