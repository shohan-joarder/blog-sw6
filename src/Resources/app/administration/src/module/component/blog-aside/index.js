import template from "./blog-aside.html.twig";

const { Component } = Shopware;

Component.register('blog-aside', {
    template,
    data() {
        return {
            options: [
                {
                    name: "Blog",
                    target: "/blog/post/list",
                },
                {
                    name: "Category",
                    target: "/blog/category/list",
                },
                {
                    name: "Author",
                    target: "/blog/author/list",
                },
            ],
        };
    },
    props: {
        activeRoute: {
            type: String, // The route being passed to the component
            required: true,
        },
    }
});
