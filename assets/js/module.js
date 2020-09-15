let page;
let parser;
let scroller;
let myModal;

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
    document.body.classList.add("loading");
    let conditions = parser.parseConditions();

    console.log("Submitting Form Input");
    let response = FormSubmission.send("/car-load-more", JSON.stringify(conditions));
    response.then(data => {
        document.body.classList.remove("loading");
        let container = document.getElementById("car-results");
        let responseElement = getElementByIdFromString(data, "car-results");
        container.innerHTML = responseElement.children.length > 0 ? responseElement.innerHTML : data;
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
    let response = FormSubmission.send("/car-form", null);
    response.then(data => {
        let json = JSON.parse(JSON.parse(data));

        myModal = modal;
        myModal.cancel = function () {
            myModal.hide();
            parser.setSettings(settings);
            $("body").removeClass("stop-scrolling");
            myModal = null;
        };
        myModal.confirm = function () {
            let carCondition = DBQuery.createCondition("id", "(SQL)(SELECT max(id) FROM car)");
            let newCarResponse = FormSubmission.send("/car-load-more", JSON.stringify([carCondition]));
            newCarResponse.then(data => {
                let tempCar = getElementByIdFromString(data, "car-results")
                document.getElementById("car-results").prepend(tempCar.getElementsByClassName("car-instance")[0]);
                reloadButtons();
                myModal.cancel();
            }); 
        };

        document.body.classList.remove("loading");

        document.getElementById('modal-content').innerHTML = "";

        let props = {
            id: "car-create-form",
            newFields: json.inputs,
            existingFields: json.selects
        };

        let form = new CreateCarUI(props);
        myModal.render(form.render());

        form.renderMore();
        form.onFormSubmit(() => { submitForm("/car-insert"); });

        let formSettings = { 
            formId: "car-create-form", 
            overides: {}, 
            dontParse: ["insert-id"]
        };

        parser.setSettings(formSettings);

        document.getElementById("modal").classList.add("update-modal");
        document.getElementById("car-create-cancel").addEventListener("click", myModal.cancel);
        
        myModal.show();
        $("body").addClass("stop-scrolling");
    });
}

function linkToCarUpdate(carId) {
    document.body.classList.add("loading");
    // let carResponse = FormSubmission.send("/car-get", carId);
    // Let's use new Callout class for this.
    let callout = new FormSubmission("/car/"+carId);
    let carResponse = callout.sendMe();
    
    let response = FormSubmission.send("/car-form", null);
    response.then(data => {

    		// Hmmm... why are we parsing twice?
        let json = JSON.parse(data);
    		console.log(json);
        myModal = modal;
        myModal.cancel = function () {
            myModal.hide();
            parser.setSettings(settings);
            $("body").removeClass("stop-scrolling");
            myModal = null;
        };
        myModal.confirm = function () {
            let carCondition = DBQuery.createCondition("id", carId);
            let updatedCarResponse = FormSubmission.send("/car-load-more", JSON.stringify([carCondition]));
            updatedCarResponse.then(data => {
                let carContainer = document.getElementById("car-container-" + carId);
                let tempCar = getElementByIdFromString(data, "car-results")
                carContainer.innerHTML = tempCar.getElementsByClassName("car-instance")[0].innerHTML;
                reloadButtons();
                myModal.cancel();
            });         
        };

        document.body.classList.remove("loading");

        document.getElementById('modal-content').innerHTML = "";

        let props = {
            id: "car-create-form",
            newFields: json.inputs,
            existingFields: json.selects
        };

        let form = new CreateCarUI(props);
        myModal.render(form.render());

        let car;
        
        carResponse.then((car) => {
            // car = JSON.parse(JSON.parse(carToUpdate));
            form.populate(car);
        });

        form.renderMore();
        form.onFormSubmit(() => { confirmUpdate(car); });

        let formSettings = { 
            formId: "car-create-form", 
            overides: {}, 
            dontParse: [] 
        };

        parser.setSettings(formSettings);

        document.getElementById("modal").classList.add("update-modal");
        document.getElementById("car-create-cancel").addEventListener("click", myModal.cancel);
        
        myModal.show();
        $("body").addClass("stop-scrolling");
    });
}

function submitForm(url) {
    document.body.classList.add("loading");        
    let conditions = parser.parseConditions();

    let response = FormSubmission.send(url, JSON.stringify(conditions));
    response.then(data => {
        document.body.classList.remove("loading");
        if (data.trim() != "") {
            document.getElementById("modal").scrollTo(0, 0);
            document.getElementById("car-create-results").innerHTML = data;
        } else {
            myModal.confirm();
        }      
    });  
};

function confirmUpdate(car) {
    let confirmText = "Are you sure you want to update the following fields?\n"

    let formFields = document.getElementsByClassName("car-create-field");

    for (let i = 0; i < formFields.length; i++) {
        let formField = formFields[i];
        let field = formField.dataset.field;
        if (!["day", "month", "year"].includes(field) && car[field]) {
            let value = car[field];
            if (field == "full_date") {
                value = value.split(" ")[0];
            }

            if (value !== formField.value) {               
                confirmText += formatLabel(field) + "\n";
            }
        }
    }

    if (confirm(confirmText)) {
        submitForm("/car-update");
    }
};

function openCarDeleteModal(carId) {
    var carToDelete = document.getElementById("car-container-" + carId);
    myModal = modal;
    myModal.renderElement = function (el) {
        document.getElementById('modal-content').innerHTML = "";
        document.getElementById('modal-content').appendChild(el);
    };
    myModal.cancel = function () {
            myModal.hide();
            $("body").removeClass("stop-scrolling");
            myModal = null;
    };
    myModal.confirm = function () {
        myModal.cancel();
        deleteCar(carId); 
        carToDelete.parentElement.removeChild(carToDelete);
        reloadButtons();
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

function formatLabel(field) {
    switch(field) {
        case "circut":
            return "circuit";
        case "subject_1":
            return "subject 1";
        case "subject_2":
            return "subject 2";
        case "full_date":
            return "date";
        default:
            return field;
    }
}

function clearEventListeners(element) {
    elClone = element.cloneNode(true);
    element.parentNode.replaceChild(elClone, element);
}