const InfiniteScroller = (function(){

    function InfiniteScroller() {}

    let moreResults = true;
    let offset = 0;

    const loadMoreResults = () => {
        if (!moreResults) {       
            return false;
        }
        let current = $(window).scrollTop();
        let bottom = $(document).height() - $(window).height();
        if(current >= bottom - 1) {
            
            let response = getNextPage(loadLimit);
    
            response.then(appendPage);
        }
    };
    
    const getNextPage = (limit) => {
        offset += limit;

        parser.setResultsOffset(offset);
        let conditions = parser.parseConditions();
    
        return FormSubmission.send("car-load-more", JSON.stringify(conditions));
    }
    
    const appendPage = (responseResults) => {
        if (responseResults == "") {
            moreResults = false;
        }               
        let container = document.getElementById("car-results");
        let results = container.innerHTML;
        let newPage = getElementByIdFromString(responseResults, "car-results");
        results += newPage.innerHTML;
        container.innerHTML = results;
        reloadButtons();
    }

    const reset = () => {
        moreResults = true;
        offset = 0;
        parser.setResultsOffset(0);
        window.scrollTo(0, 0);
    }

    let proto = {
        loadMoreResults: loadMoreResults,
        reset: reset
    }

    InfiniteScroller.prototype = proto;

    return InfiniteScroller;
})();