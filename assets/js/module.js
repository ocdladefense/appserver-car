let page;
let parser;
let scroller;
let myModal;
let modalForm;

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
    reloadButtons();

    page.onUserSearch(sendQuery);

    style();
};

window.onresize = style;

function sendQuery() {
    document.body.classList.add("loading");
    let conditions = parser.parseConditions();

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
    page.addFeature("carUpdate", openCarUpdateModal);
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

    if (modalForm) {
        modalForm.styleForm(900);
    }
}

function buildModalForm() {
    document.body.classList.add("loading");
    let response = FormSubmission.send("/car-form", null);
    return response.then(data => {
        let json = JSON.parse(data);

        myModal = modal;

        document.body.classList.remove("loading");

        document.getElementById('modal-content').innerHTML = "";








        // Existing values
        let props = {
            id: "car-create-form",
            newFields: json.inputs,
            existingFields: json.selects
        };

        modalForm = new CreateCarUI(props);




        // application.render(modalForm.render(), document.getElementById('modal-content'));
        myModal.render(modalForm.render());
        modalForm.attachSelectEvents();










        document.getElementById("modal").classList.add("update-modal");

        myModal.cancel = function () {
            closeModalForm();
        };
        document.getElementById("car-create-cancel").addEventListener("click", myModal.cancel);

        myModal.show();
        modalForm.styleForm(900);
        $("body").addClass("stop-scrolling");
    });
}

function closeModalForm() {
    myModal.hide();
    parser.setSettings(settings);
    $("body").removeClass("stop-scrolling");
    myModal = null;
    modalForm = null;
}

"SELECT * from car where id = (SELECT max(id) FROM car)"
"SELECT * from car where id in (SELECT max(id) FROM car)"

function openCarCreateModal() {
    let response = buildModalForm();
    response.then(() => {
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

        let formSettings = { 
            formId: "car-create-form", 
            overides: {}, 
            dontParse: ["id-input"]
        };

        parser.setSettings(formSettings);

        modalForm.onFormSubmit(() => { submitForm("/car-insert"); });
    });
}


function openCarUpdateModal(carId) {
    let callout = new FormSubmission("/car/"+carId);
    let carResponse = callout.sendMe();
    let response = buildModalForm();
    response.then(() => {

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


        let formSettings = { 
            formId: "car-create-form", 
            overides: {}, 
            dontParse: [] 
        };

        parser.setSettings(formSettings);

        carResponse.then((car) => {
            modalForm.populate(car);
            modalForm.onFormSubmit(() => { confirmUpdate(car); });
        });
        
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

    if (carToDelete.getElementsByClassName("ellipsis")[0]) {
        carToDelete.getElementsByClassName("ellipsis")[0].style.display = "inline";
        carToDelete.getElementsByClassName("readMoreButton")[0].innerHTML = "Read more";
        carToDelete.getElementsByClassName("more")[0].style.display = "none";
    }
    
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
    let checkboxes = document.getElementsByClassName("searchbox-checkbox");
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
