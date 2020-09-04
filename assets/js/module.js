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
    page.addFeature("carCreate", openCarCreateModal);
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
        let responseElement = getElementByIdFromString(data, "car-results");
        container.innerHTML = responseElement ? responseElement.innerHTML : data;
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

function openCarCreateModal() {
    let response = FormSubmission.send("/car-create", null);
    response.then(data => {
        document.getElementById("car-results").innerHTML = data;
    });
}

function linkToCarUpdate(carId) {
    document.body.classList.add("loading");
    //let response = FormSubmission.send("/car-update?carId=" + carId, null);
    let carResponse = FormSubmission.send("/car-get", carId);
    let response = FormSubmission.send("/car-form", null);
    response.then(data => {
        let json = JSON.parse(JSON.parse(data));

        let myModal = modal;
        //addModalFunctions(myModal);
        myModal.cancel = function () {
            myModal.hide();
            parser.setSettings(settings);
            $("body").removeClass("stop-scrolling");
        };
        myModal.confirm = function () {
            //updateHtml(json.car);
            let carCondition = DBQuery.createCondition("id", carId);
            console.log(carCondition);
            let updatedCarResponse = FormSubmission.send("/car-results", JSON.stringify([carCondition]));
            updatedCarResponse.then(data => {
                let carContainer = document.getElementById("car-container-" + carId);
                let tempCar = getElementByIdFromString(data, "car-results")
                carContainer.innerHTML = tempCar.getElementsByClassName("car-instance")[0].innerHTML;
                //let newCar = data.getElementById("car-container-" + carId);
                //carContainer.innerHTML = newCar.innerHTML;
                reloadButtons();
                myModal.cancel();
            });         
        };

        document.body.classList.remove("loading");

        document.getElementById('modal-content').innerHTML = "";

        json.modal = myModal;
        //new CarCreateModule(json);

        let props = {
            id: "car-create-form",
            newFields: json.inputs,
            existingFields: json.selects,
            //isUpdate: responseData.update,
            //car: responseData.car
        };

        let form = new CreateCarUI(props);
        myModal.render(form.render());

        carResponse.then((carToUpdate) => {
            form.populate(JSON.parse(carToUpdate));
        });

        form.renderMore();

        document.getElementById("modal").classList.add("update-modal");
        document.getElementById("car-create-cancel").addEventListener("click", myModal.cancel);
        
        myModal.show();
        $("body").addClass("stop-scrolling");
    });
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