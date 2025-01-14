const searchElements = document.getElementsByName("search");
let typingTimer;                // Timer identifier
const doneTypingInterval = 500; // Time in milliseconds (1 second)

searchElements.forEach(element => {
    element.addEventListener("keyup", function() {
       
        clearTimeout(typingTimer);  // Clear the previous timer on each keystroke
        typingTimer = setTimeout(async () => {
            
            const searchValue = this.value;

            const response = await fetch(`/gisl-blog-search`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    searchTerm: searchValue
                })
            });

            const {count,sarch_url} = await response.json();

            count > 0 && document.querySelectorAll('.search-suggest-no-result').forEach(element => {
                element.remove();
            });
            document.querySelectorAll('.blogResult').forEach(element => element.remove());
            const searchSuggestionContainer = document.querySelector(".search-suggest-container");
            
            if (count > 0 && searchSuggestionContainer) {
                searchSuggestionContainer.insertAdjacentHTML('beforeend', `
                    <li class="js-result search-suggest-total blogResult">
                        <div class="row align-items-center g-0">
                            <div class="col">
                                <a href="${count > 0 ? sarch_url : "#"}" title="Show all search results" class="search-suggest-total-link">
                                <span class="icon icon-arrow-head-right icon-sm">
                                    <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24" height="24" viewBox="0 0 24 24"><defs><path id="icons-default-arrow-head-right" d="m11.5 7.9142 10.2929 10.293c.3905.3904 1.0237.3904 1.4142 0 .3905-.3906.3905-1.0238 0-1.4143l-11-11c-.3905-.3905-1.0237-.3905-1.4142 0l-11 11c-.3905.3905-.3905 1.0237 0 1.4142.3905.3905 1.0237.3905 1.4142 0L11.5 7.9142z"></path></defs><use transform="rotate(90 11.5 12)" xlink:href="#icons-default-arrow-head-right" fill="#758CA3" fill-rule="evenodd"></use></svg>
                                </span>
                                    ${count > 0 ? "Show blog search result" : "No blog found"} 
                                </a>
                            </div>
                            <div class="col-auto search-suggest-total-count">
                                ${count} Result
                            </div>
                        </div>
                    </li>
                `);
            }

        }, doneTypingInterval);
    });

    element.addEventListener("keydown", function() {
        clearTimeout(typingTimer); // Clear the timer on key down to reset the delay
    });
});