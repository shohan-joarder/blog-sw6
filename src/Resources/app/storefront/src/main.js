// import './snippet/de_DE/gisl.de-DE.json';
// import './snippet/en_GB/gisl.en-GB.json';

$(document).ready(function () {

    // Highlight the active TOC link based on scrolling
    const updateStickySidebar = ()=>{
        const windowWidth = $(window).width();

        if (windowWidth >= 992) {
            $(window).on("scroll", function () {
                //Table content and right side stikcy
                const $tableOfContents = $("#tableOfContents");
                const $endOfBlogContentEl = $("#endOfBlogContentArea");

                // Table content sticky
                const $container = $("#blogDetailsContentArea");

                const containerTop = $container.offset().top - $(window).scrollTop();
                const endSectionTop = $endOfBlogContentEl.offset().top - $(window).scrollTop();

                if (containerTop <= 0 && endSectionTop > $(window).height()) {
                    $tableOfContents.css({
                        position: "fixed",
                        top: "20px", // Adjust for spacing
                        width: $tableOfContents.parent().width(), // Match parent column width
                    });
                } else {
                    $tableOfContents.css({
                        position: "static",
                        top: "0px",
                        width: "100%",
                    });
                }

                // Blog right sticky
                const $tableOfContentsBlogRight = $("#detailsRightArea");

                const containerTopBlogRight = $container.offset().top - $(window).scrollTop();
                const endSectionTopBlogRight = $endOfBlogContentEl.offset().top - $(window).scrollTop();

                if (containerTopBlogRight <= 0 && endSectionTopBlogRight > $(window).height()) {
                    $tableOfContentsBlogRight.css({
                        position: "fixed",
                        top: "20px", // Adjust for spacing
                        width: $tableOfContentsBlogRight.parent().width(), // Match parent column width
                    });
                } else {
                    $tableOfContentsBlogRight.css({
                        position: "static",
                        top: "0px",
                        width: "100%",
                    });
                }

            })

        }
    }
    updateStickySidebar()

    $(window).on('scroll', function () {

        //Scroll to active table content  functionality
        let scrollPosition = $(window).scrollTop();
        $('#tableOfContents a').each(function () {
            // Escape the href value
            let sectionId = $(this).attr('href');
            let section = $(document.getElementById(sectionId.substring(1))); // Safe selection
            if (section.length) {
                let sectionTop = section.offset().top - 42; // Adjust offset for better accuracy
                let sectionBottom = sectionTop + section.outerHeight();

                if (scrollPosition >= sectionTop && scrollPosition < sectionBottom) {
                    $('#tableOfContents a').removeClass('active_content');
                    $(this).addClass('active_content');
                }
            }
        });
    });

    // Ensure responsiveness on window resize
    $(window).on("resize", function () {
        updateStickySidebar()
    });

    // Smooth scroll and set active class on click
    $('#tableOfContents a').on('click', function (e) {
        e.preventDefault();
        let targetId = $(this).attr('href');
        let target = $(document.getElementById(targetId.substring(1))); // Safe selection
        if (target.length) {
            $('#tableOfContents a').removeClass('active_content'); // Remove active class from all links
            $(this).addClass('active_content'); // Add active class to the clicked link
            $('html, body').animate(
                {
                    scrollTop: target.offset().top - 10 // Adjust offset to align better
                },
                500 // Animation duration (in milliseconds)
            );
        }
    });

});


// Add any other imports (e.g., SCSS or JS)
// document.addEventListener("scroll", () => {
//
//     //Table content sticky
//     const container = document.querySelector("#blogDetailsContentArea");
//     const tableOfContents = document.getElementById("tableOfContents");
//     const endOfBlogContentEl = document.querySelector("#endOfBlogContentArea");
//
//     // Get the position of the container relative to the viewport
//     const containerTop = container.getBoundingClientRect().top;
//     const endSectionTop = endOfBlogContentEl.getBoundingClientRect().top + 200;
//
//     // If the container hits the top of the screen, make the table of contents sticky
//     if (containerTop <= 0 && endSectionTop > window.innerHeight) {
//         tableOfContents.style.position = "fixed";
//         tableOfContents.style.top = "20px"; // Adjust offset from the top
//         tableOfContents.style.width = "280px";
//     } else {
//         // Reset to static when the container is below the viewport
//         tableOfContents.style.position = "static";
//         tableOfContents.style.top = "0px";
//         tableOfContents.style.width = "100%";
//     }
// });

// document.addEventListener("DOMContentLoaded", () => {
//     const links = Array.from(document.querySelectorAll("#tableOfContents a")); // Ensure it's an array
//     const headings = Array.from(document.querySelectorAll("[id]")).filter(el =>
//         links.some(link => link.getAttribute("href") === `#${el.id}`)
//     );
//
//     function updateActiveLink() {
//         let currentActive = null;
//         let smallestDiff = Infinity;
//         const offset = 40; // Add space above the heading
//
//         headings.forEach((heading, index) => {
//             const bounding = heading.getBoundingClientRect();
//
//             // Check if the heading is within the desired range and not at the bottom of the viewport
//             if (
//                 bounding.top >= offset && // Add space
//                 bounding.top <= window.innerHeight - bounding.height / 2 // Ensure not at the bottom
//             ) {
//                 const diff = Math.abs(bounding.top - offset);
//                 if (diff < smallestDiff) {
//                     smallestDiff = diff;
//                     currentActive = index;
//                 }
//             }
//         });
//
//         // Remove "active" class from all links
//         links.forEach(link => link.classList.remove("active_content"));
//
//         // Add "active" class to the current link
//         if (currentActive !== null) {
//             links[currentActive].classList.add("active_content");
//         }
//     }
//
//     // Attach the scroll listener
//     window.addEventListener("scroll", updateActiveLink);
// });
