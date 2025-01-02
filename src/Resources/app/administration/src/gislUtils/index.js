Shopware.Utils.GlobalFunctions = {

    appLanguage :function(){
        const languageId = Shopware.Context.api.systemLanguageId;
        return languageId;
    },

    generateSlug:function(text) {
        let slug= text
            .toString()
            .toLowerCase() // Convert to lowercase
            .trim() // Remove whitespace from both ends
            .normalize('NFD') // Handle special characters like accents
            .replace(/[\u0300-\u036f]/g, '') // Remove diacritics
            .replace(/[^a-z0-9 ]/g, '') // Remove special characters
            .replace(/\s+/g, '-') // Replace spaces with hyphens

        return slug;
    },

    getActiveLangData: async  function(type,fkId,langId, defaultValue=false){
        // this.appLanguage()
        try {
            // Ensure type and fkId are provided
            if (!type || !fkId) {
                throw new Error('Type and fkId are required.');
            }
    
            // Create Criteria to fetch language-specific data
            const criteria = new Shopware.Data.Criteria();
            criteria.addFilter(Shopware.Data.Criteria.equals('type', type));
            criteria.addFilter(Shopware.Data.Criteria.equals('fkId', fkId));
            criteria.addFilter(Shopware.Data.Criteria.equals('languageId', langId)); // Get current language
    
            // Fetch data from the translation repository
            const repository = Shopware.Service('repositoryFactory').create('gisl_blog_translation');
            const result = await repository.search(criteria, Shopware.Context.api);
    
            // Return the first result if found, or null if not found
            if (result && result.length > 0) {
                return result[0];
            } else {
                if (defaultValue) {
                    return this.getActiveLangData(type,fkId,this.appLanguage())
                }
                console.warn(`No active language data found for type: ${type} and fkId: ${fkId}`);
                return null;
            }
        } catch (error) {
            console.error('Error fetching active language data:', error);
            return null;
        }

    },

    storeLanguageData: async function (fkId, data={}) {
        try {
           const  {type, langId, title,slug, shortDescription, description, languageIdPk ,meta_title, meta_description, meta_keywords }  =  data


            // Access the repository for the `gisl_blog_translation` entity
            const languageRepository =  Shopware.Service('repositoryFactory').create('gisl_blog_translation');

            let translationToSave;
    
            // Check if `languageIdPk` exists to perform update or create
            if (languageIdPk) {
                // UPDATE: Fetch the existing entity and update fields
                translationToSave = await languageRepository.get(languageIdPk, Shopware.Context.api);
    
                if (!translationToSave) {
                    console.error('Translation not found with the provided ID:', languageIdPk);
                    return false;
                }
            } else {
                // CREATE: Create a new entity
                translationToSave = languageRepository.create(Shopware.Context.api);
            }
    
            // Fill in the fields with the appropriate data
            translationToSave.fkId = fkId; // Foreign key linking to the saved entity
            translationToSave.languageId = langId || this.appLanguage(); // Active language
            translationToSave.type = type; // Specify type (e.g., 'category')
            translationToSave.title = title || ''; // Title of the category/blog
            translationToSave.slug = slug || ''; // Title of the category/blog
            translationToSave.shortDescription = shortDescription || ''; // Short description
            translationToSave.description = description || ''; // Full description
            translationToSave.meta_title = meta_title || ''; // 
            translationToSave.meta_description = meta_description || ''; // 
            translationToSave.meta_keywords = meta_keywords || ''; // 
            translationToSave.updatedAt = new Date().toISOString(); // Timestamp when the entry was updated/created
    
            // Save the language-specific data using the repository
            await languageRepository.save(translationToSave, Shopware.Context.api);
    
            return true;
        } catch (error) {
            console.error('An error occurred while saving language data:', error);
            return false;
        }
    },

    validateUniqueSlug: async function (slug, trId) {
        
        const criteria = new Shopware.Data.Criteria();
    
        // Add a filter to match the slug
        criteria.addFilter(Shopware.Data.Criteria.equals('slug', slug));
    
        // Exclude the current item if it's an update operation
        if (trId) {
            criteria.addFilter(
                Shopware.Data.Criteria.not('AND', [
                    Shopware.Data.Criteria.equals('id', trId),
                ])
            );
        }
    
        // Access the repository for the entity
        const repository = Shopware.Service('repositoryFactory').create('gisl_blog_translation');
    
        try {
            // Perform the search using the criteria
            const result = await repository.search(criteria, Shopware.Context.api);
    
            // Check if any matching slugs are found
            if (result.total > 0) {
                return {
                    isValid: false,
                    error: "The slug you entered already exists",
                };
            }
    
            return {
                isValid: true,
                error: null,
            };
        } catch (error) {
            console.error('Error validating unique slug:', error);
            return {
                isValid: false,
                error: "An error occurred while validating the slug",
            };
        }
    }
    
};