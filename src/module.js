let parser;

window.onload = () => {

    let parser = new FormParserComponent(SEARCH_WIDGET_SETTINGS);

    // Application.init();

    document.addEventListener('click', (e) => {
        let target = e.target;
        let parent = target.parentNode;

        let ellipsis = parent.querySelector(".ellipsis");
        let moreText = parent.querySelector(".more");
        let btnText = parent.querySelector(".readMoreButton");

        readMore(ellipsis, moreText, btnText);
    } );


    parser.render();

    
};


