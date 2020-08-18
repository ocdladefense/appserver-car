let page;
let parser;

let settings = { 
    formId: "car-create-form", 
    overides: { "car-create-token": parseToken }, 
    dontParse: [] 
};

window.onload = () => {
    page = new CreateCarUI();

    parser = new FormParser();
    parser.setSettings(settings);

    page.render();

    let submitFunction = isUpdate ? confirmUpdate : submitForm;
    page.onFormSubmit(submitFunction);

    style();
}

function submitForm(url = "/car-submit") {        
    let conditions = parser.parseConditions();

    let response = FormSubmission.send(url, JSON.stringify(conditions));
    response.then(data => {
        window.location.href = '../cars';
    });  
}

function confirmUpdate() {
    let confirmText = "Are you sure you want to update the following fields?\n"

    let formFields = document.getElementsByClassName("car-create-field");

    for (let i = 0; i < formFields.length; i++) {
        let formField = formFields[i];
        let field = formField.dataset.field;
        if (car[field] && car[field] !== formField.value) {
            confirmText += field + "\n";
        }
    }

    if (confirm(confirmText)) {
        submitForm("/car-submit-update");
    }
}

function style() {
    let fields = document.getElementsByClassName("form-field");
    for (let i = 0; i < fields.length; i++) {
        let field = fields[i];
        let children = field.childNodes;
        if (children[1].tagName != "TEXTAREA") {
            continue;
        }

        let inputHeight = children[1].offsetHeight + "px";
        children[0].style.height = inputHeight;
    }
}

function parseToken() {
    let tokenInput = document.getElementById("car-create-token");
    return {
        type: "token",
        value: tokenInput.value + ""
    }
}