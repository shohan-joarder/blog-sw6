// import './snippet/de_DE/gisl.de-DE.json';
// import './snippet/en_GB/gisl.en-GB.json';

// Add any other imports (e.g., SCSS or JS)
document.addEventListener("scroll", () => {

    //Table content sticky
    const container = document.querySelector("#blogDetailsContentArea");
    const tableOfContents = document.getElementById("tableOfContents");
    const endOfBlogContentEl = document.querySelector("#endOfBlogContentArea");

    // Get the position of the container relative to the viewport
    const containerTop = container.getBoundingClientRect().top;
    const endSectionTop = endOfBlogContentEl.getBoundingClientRect().top + 200;

    // If the container hits the top of the screen, make the table of contents sticky
    if (containerTop <= 0 && endSectionTop > window.innerHeight) {
        tableOfContents.style.position = "fixed";
        tableOfContents.style.top = "20px"; // Adjust offset from the top
        tableOfContents.style.width = "280px";
    } else {
        // Reset to static when the container is below the viewport
        tableOfContents.style.position = "static";
        tableOfContents.style.top = "0px";
        tableOfContents.style.width = "100%";
    }
});