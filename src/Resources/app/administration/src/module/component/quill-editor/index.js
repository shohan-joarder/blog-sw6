import template from './quill-editor.html.twig';

const { Component } = Shopware;

Component.register('quill-editor', {
    template,

    props: {
        name: {
            type: String,
            required: true,
        },
        value: {
            type: String,
            default: '',
        },
    },

    data() {
        return {
            editor: null,
        };
    },

    watch: {
        value(newValue) {
            if (this.editor && this.editor.root.innerHTML !== newValue) {
                this.editor.root.innerHTML = newValue;
            }
        },
    },

    mounted() {
        this.loadQuillResources().then(() => {
            this.initializeEditor();
        });
    },

    methods: {
        loadQuillResources() {
            const loadScript = (src) => {
                return new Promise((resolve, reject) => {
                    const script = document.createElement('script');
                    script.src = src;
                    script.onload = resolve;
                    script.onerror = reject;
                    document.head.appendChild(script);
                });
            };

            const loadCSS = (href) => {
                return new Promise((resolve, reject) => {
                    const link = document.createElement('link');
                    link.rel = 'stylesheet';
                    link.href = href;
                    link.onload = resolve;
                    link.onerror = reject;
                    document.head.appendChild(link);
                });
            };

            return Promise.all([
                loadCSS('https://cdn.quilljs.com/1.3.7/quill.snow.css'),
                loadScript('https://cdn.quilljs.com/1.3.7/quill.min.js'),
            ]);
        },
        initializeEditor() {
            this.editor = new Quill(this.$refs.quillContainer, {
                theme: 'snow',
                modules: {
                    toolbar: [
                        
                        // Headings
                        [{ header: [1, 2, 3, 4, 5, 6, false] }],
                        // Colors
                        [{ color: [] }, { background: [] }], 
                        // Basic formatting
                        ['bold', 'italic', 'underline', 'strike'], // toggled buttons
                        // Font and size
                        [{ font: [] }],
                        [{ size: [] }], // dropdown with default sizes (small, normal, large, huge)
                    
                        // Text alignment
                        [{ align: [] }],
                    
                        // Subscript/Superscript
                        // ['sub', 'super'], 
                    
                        // Lists and Indentation
                        [{ list: 'ordered' }, { list: 'bullet' }],
                        [{ indent: '-1' }, { indent: '+1' }], // outdent/indent
                    
                        // Blocks
                        ['blockquote'],
                        // , 'code-block'
                        // Links, Images, Videos
                        ['link', 'image','video'],
                    
                        // Clear formatting
                        ['clean'] // remove formatting button
                    ]
                    
                },
            });

            this.editor.root.innerHTML = this.value;

            this.editor.on('text-change', () => {
                this.$emit('input', this.editor.root.innerHTML);
            });
        },
    },
});
