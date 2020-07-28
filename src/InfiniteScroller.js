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
        
        let conditions = parser.parseConditions();

        //Revist to match syntax in module.js
        //conditions.push(parser.parseLimitInput([loadLimit, offset]));
    
        return FormSubmission.send("car-load-more", JSON.stringify(conditions));
    }
    
    const appendPage = (responseResults) => {
        if (responseResults == "") {
            moreResults = false;
        }               
        let container = document.getElementById("car-results");
        let results = container.innerHTML;
        results += responseResults;
        container.innerHTML = results;
    }

    const reset = () => {
        moreResults = true;
        offset = 0;
        window.scrollTo(0, 0);
    }

    let proto = {
        loadMoreResults: loadMoreResults,
        reset: reset
    }

    InfiniteScroller.prototype = proto;

    return InfiniteScroller;
})();