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
        document.getElementById("car-form").style.display = "block";
    } else {
        let topStyle = (document.getElementById("header").offsetHeight - 2) + "px";
        document.getElementById("car-form").style.top = topStyle;
    }
}

//Called by an eventhandler declared on the template
function linkToCarUpdate(carId) {
    let url = "car-update?carId=" + carId;
    window.location.href = url;
}

function linkToCarDelete(carId) {

    let response = FormSubmission.send("/car-delete", carId);
    response.then(data => {
        /*let container = document.getElementById("car-results");
        container.innerHTML = data;

        confirmDelete(carId);*/
        
        var carToDelete = document.getElementById("car-container-" + carId);
        var myModal = new Modal({}, false);
        console.log(myModal);
        myModal.render(carToDelete.cloneNode(true));
        //myModal.content = carToDelete.cloneNode(true);
        myModal.cancel = function () { return false; };
        myModal.submit = function () { 
            deleteCar(carId); 
            carToDelete.parentElement.removeChild(carToDelete);
        };
        
        /*let promise = new Promise((resolve, reject) => {
            addHtml();
            resolve("done");
        });

        //promise.then(() => {
        //    confirmDelete(carId);
        //});
        
        let result = await promise;

        alert(result);*/
        
    });
}

function confirmDelete(carId) {
    if (confirm("Are you sure you want to permanently delete this Criminal Apellate Review?")) {
        deleteCar(carId);
    } else {
        window.location.href = "/cars";
    }
};

function deleteCar(carId) {
    let whereCondition = DBQuery.createCondition("id", carId);

    let response = FormSubmission.send("/car-delete-submit", JSON.stringify(whereCondition));
    response.then(data => {
        console.log(data);
        window.location.href = "/cars";
    });
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