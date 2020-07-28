let page;
let parser;
let scroller;

window.onload = () => {
    page = new PageUI();

    //FormParser extracts querry data from the Form
    parser = new FormParser();
    parser.setResultsLimit(loadLimit);

    scroller = new InfiniteScroller();

    page.render();
    
    page.addFeature("readMoreSummary", page.readMoreClick);
    page.addFeature("infiniteScroll", scroller);
    page.addFeature("searchBoxPlaceholder", searchPlaceholderText);

    page.onUserSearch(sendQuery);

    style();
};

window.onresize = style;

function sendQuery() {
    let conditions = parser.parseConditions();

    console.log("Submitting Form Input");
    let response = FormSubmission.send("/car-results", JSON.stringify(conditions));
    response.then(data => {
        let container = document.getElementById("car-results");
        container.innerHTML = data;
    });
}

function style() {
    if (window.innerWidth >= 900) {

    } else {
        let topStyle = (document.getElementById("header").offsetHeight - 2) + "px";
        document.getElementById("car-form").style.top = topStyle;
    }
}