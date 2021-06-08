const InfiniteScroller = (function(){

    function InfiniteScroller() {}

    let moreResults = true;
    let isLoading = false;
    let offset = 0;
    //loadlimit
    //car-load-more
    //car-results

    const loadMoreResults = () => {
        if (!moreResults) {       
            return false;
        }
        let current = $(window).scrollTop();
        let bottom = $(document).height() - $(window).height();
        if(current >= bottom - 1 && !isLoading) {
            addLoadingScreen();
            
            let response = getNextPage(loadLimit);
    
            return response.then(appendPage);
        }
    };
    
    const getNextPage = (limit) => {
        offset += limit;

        parser.setResultsOffset(offset);
        let conditions = parser.parseConditions();
    
        return FormSubmission.send("car-load-more", JSON.stringify(conditions));
    }
    
    const appendPage = (responseResults) => {
        if (isLoading) {
            removeLoadingScreen();
        }
        if (responseResults == "") {
            moreResults = false;
        }               
        let container = document.getElementById("car-results");
        let results = container.innerHTML;
        let newPage = getElementByIdFromString(responseResults, "car-results");
        results += newPage.innerHTML;
        container.innerHTML = results;
        return true;
    }

    const addLoadingScreen = () => {
        isLoading = true;
        document.body.classList.add("loading");
    };

    const removeLoadingScreen = () => {
        isLoading = false;
        document.body.classList.remove("loading");
    };

    const reset = () => {
        moreResults = true;
        offset = 0;
        parser.setResultsOffset(0);
        window.scrollTo(0, 0);
    }

    const getElementByIdFromString = (string, id) => {
        let temp = createElement(vNode(
            "div",
            {},
            []
        ));
    
        temp.innerHTML = string;
        return temp.querySelector("#" + id);
    };

    let proto = {
        loadMoreResults: loadMoreResults,
        reset: reset
    }

    InfiniteScroller.prototype = proto;

    return InfiniteScroller;
})();