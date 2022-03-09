
domReady(initSearch);

const initSearch = function() {

    // add the event listeners to anu searches that use the "judge-datalist"...datalist.
    // let judgeSearches = document.querySelectorAll("[list='judges']");
    
    let form = document.getElementById("search-form");
    let reset;
    let clear;


    form.addEventListener("change", function(e) {
        e.currentTarget.submit();
    });




    // 
    document.getElementById("resetBtn").addEventListener("click", function(){
        window.location.replace();
    });

};




