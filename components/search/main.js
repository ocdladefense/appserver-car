/**
 * @main.js
 * 
 * Initialize event handlers to perform a record search.
 * 
 */

const initSearch = function() {

    let form = document.getElementById("record-search");
    let reset = document.getElementById("search-reset");
    let summarize = document.getElementById("summarize");


    form.addEventListener("change", function(e) {
        e.currentTarget.submit();
    });


    let resetFn = function(e) {
        e.preventDefault();
        e.stopPropagation();
        window.location.assign("/car/list");
    };

    let toggleSummarize = function(e) {
        let target = e.target;
        let current = e.target.value;
        let next = current == "1" ? "0" : "1";

        // target.classList.toggle("active");
        target.value = next;
    };


    reset.addEventListener("click",resetFn);
    summarize.addEventListener("click", toggleSummarize);
};


domReady(initSearch);





/*
// add the event listeners to anu searches that use the "judge-datalist"...datalist.
let judgeSearches = document.querySelectorAll("[data-datalist='judge-datalist']");

for(let i = 0; i < judgeSearches.length; i++){

    judgeSearches[i].addEventListener("keyup", minimumCharacterSearch);
}


function minimumCharacterSearch(e){

    let inputElem = e.target;
    let dataListId = inputElem.getAttribute("data-datalist")
    let inputValue = e.target.value;

    if(inputValue.length >= 1){

        inputElem.setAttribute("list", dataListId);

    } else {

        inputElem.removeAttribute("list");
    }

}

*/