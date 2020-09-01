let page;
let parser;
let scroller;

//settings descript how the form parser should interpret each form field
let settings = {
    formId: "car-form",
    overides: {
        "car-search-box": subject1CustomParse
    },
    dontParse: ["checkbox-group"]
}

window.onload = () => {
    page = new PageUI();

    //FormParser extracts querry data from the Form
    parser = new FormParser();
    parser.setSettings(settings);
    parser.setResultsLimit(loadLimit);

    scroller = new InfiniteScroller();

    page.render();
    
    page.addFeature("readMoreSummary", page.readMoreClick);
    page.addFeature("infiniteScroll", scroller);
    page.addFeature("searchBoxPlaceholder", searchPlaceholderText);
    page.addFeature("carUpdate", linkToCarUpdate);
    page.addFeature("carDelete", openCarDeleteModal);

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
        container.innerHTML = getElementByIdFromString(data, "car-results").innerHTML;
        reloadButtons();
    });
}

function reloadButtons() {
    page.addFeature("carUpdate", linkToCarUpdate);
    page.addFeature("carDelete", openCarDeleteModal);
}

function style() {
    page.displayForm();
    if (window.innerWidth >= 900) {
        document.getElementById("car-form").style.display = "block";
    } else {
        let topStyle = (document.getElementById("header").offsetHeight - 2) + "px";
        document.getElementById("car-form").style.top = topStyle;
    }
}

function styleModal() {
    let carModal = document.getElementById("modal");
    carModal.style.top = "10%";
}

function linkToCarUpdate(carId) {
    let url = "car-update?carId=" + carId;
    window.location.href = url;
}

function openCarDeleteModal(carId) {
    var carToDelete = document.getElementById("car-container-" + carId);
    var myModal = modal;
    myModal.renderElement = function (el) {
        document.getElementById('modal-content').innerHTML = "";
        document.getElementById('modal-content').appendChild(el);
    };
    myModal.cancel = function () {
            myModal.hide();
            $("body").removeClass("stop-scrolling");
    };
    myModal.confirm = function () {
        myModal.cancel();
        deleteCar(carId); 
        carToDelete.parentElement.removeChild(carToDelete);
    };
    myModelElement = carToDelete.cloneNode(true);
    myModelElement = addModalElements(myModelElement);
    myModal.renderElement(myModelElement);
    myModal.show();
    document.getElementById("car-modal-cancel").addEventListener("click", (e) => {
        e.preventDefault();
        myModal.cancel();
    });
    document.getElementById("car-modal-confirm").addEventListener("click", (e) => {
        e.preventDefault();
        myModal.confirm();
    });
    $("body").addClass("stop-scrolling");
}

function deleteCar(carId) {
    let whereCondition = DBQuery.createCondition("id", carId);

    FormSubmission.send("/car-delete", JSON.stringify(whereCondition));
}

function addModalElements(myModal) {
    let completeModal = createElement(vNode(
        "div",
        { id: myModal.id },
        []
    ));
    let modalBody = createElement(vNode(
        "div",
        { id: "car-modal-body" },
        []
    ));
    for (let i = 0; i < myModal.children.length; i++) {
        modalBody.appendChild(myModal.children[i].cloneNode(true));
    }

    completeModal.appendChild(createElement(vNode(
        "h4",
        { id: "car-modal-header" },
        "Are you sure you want to permanently delete this Criminal Apellate Review?"
    )));
    completeModal.appendChild(modalBody);
    completeModal.appendChild(createElement(vNode(
        "div",
        { id: "car-modal-footer" },
        [
            vNode(
                "a",
                { id: "car-modal-cancel", class: "car-model-button", href: "#" },
                [vNode(
                    "span",
                    {},
                    "Cancel"
                )]
            ),
            vNode(
                "a",
                { id: "car-modal-confirm", class: "car-model-button", href: "#" },
                [vNode(
                    "span",
                    {},
                    "Yes, Delete"
                )]
            )
        ]
    )));
    return completeModal;
}

function subject1CustomParse(data) {
    let checkboxes = document.getElementsByClassName("search-checkbox");
    let searchConditions = [];
    for(let i = 0; i < checkboxes.length; i++) {
        let checkbox = checkboxes[i];
        if (checkbox.checked) {
            let searchTerms = DBQuery.createTerms(data.value);
            let conditions = [];
    
            searchTerms.forEach(term => {
                conditions.push(DBQuery.createCondition(checkbox.value, term, "LIKE"));
            });

            searchConditions.push(...conditions);
        }
    }
    
    if (searchConditions.length == 1) {
        return searchConditions[0];
    } else if (searchConditions.length > 1) {
        return searchConditions;
    }
}

function getElementByIdFromString (string, id) {
    let temp = createElement(vNode(
        "div",
        {},
        []
    ));

    temp.innerHTML = string;
    return temp.querySelector("#" + id);
};

function readMore(ellipsis, moreText, btnText) {

    if (ellipsis.style.display === "none") {
        ellipsis.style.display = "inline";
        btnText.innerHTML = "Read more";
        moreText.style.display = "none";
      } else {
        ellipsis.style.display = "none";
        btnText.innerHTML = "Read less";
        moreText.style.display = "inline";
      }
}