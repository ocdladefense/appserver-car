/**
 * @main.js
 * 
 * Initialize event handlers to perform a record search.
 * 
 */

const initSearch = function() {

    let form = document.getElementById("record-search");
    let reset = document.getElementById("search-reset");
    


    form.addEventListener("change", function(e) {
        e.currentTarget.submit();
    });


    resetFn = function(e) {
        e.preventDefault();
        e.stopPropagation();
        window.location.assign("/car/list");
    };

    reset.addEventListener("click",resetFn);
};


domReady(initSearch);