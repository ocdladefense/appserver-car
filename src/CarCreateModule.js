let page;
let parser;

let settings = { 
    formId: "car-create-form", 
    overides: {}, 
    dontParse: [] 
};

window.onload = () => {
    page = new CreateCarUI();

    parser = new FormParser();
    parser.setSettings(settings);

    page.render();
    page.onFormSubmit(submitForm);

    style();
}

function submitForm() {        
    let conditions = parser.parseConditions();

    let response = FormSubmission.send("/car-submit", JSON.stringify(conditions));
    response.then(data => {
        window.location.href = '../cars';
        //let container = document.getElementById("results");
        //container.innerHTML = data;
    });  
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