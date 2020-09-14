const CarCreateModule = (function() {

    let page;
    let parser;

    let isUpdate = true;
    let car;
    let newFields;
    let existingFields;
    let myModal;

    function CarCreateModule(responseData) {
        isUpdate = responseData.update;
        car = responseData.car;
        myModal = responseData.modal;

        let props = {
            id: "car-create-form",
            newFields: responseData.inputs,
            existingFields: responseData.selects,
            isUpdate: responseData.update,
            car: responseData.car
        };

        page = new CreateCarUI(props);

        parser = new FormParser();
        parser.setSettings(settings);

        page.render();

        let submitFunction = isUpdate ? confirmUpdate : submitForm;
        page.onFormSubmit(submitFunction);

        style();
    }

    const submitForm = (url = "/car-submit") => {        
        let conditions = parser.parseConditions();
        console.log(conditions);

        let response = FormSubmission.send(url, JSON.stringify(conditions));
        response.then(data => {
            if (data != "") {
                document.getElementById("modal").scrollTo(0, 0);
                document.getElementById("car-create-results").innerHTML = data;
            } else {
                myModal.confirm();
            }      
        });  
    };

    const confirmUpdate = () => {
        let confirmText = "Are you sure you want to update the following fields?\n"

        let formFields = document.getElementsByClassName("car-create-field");

        for (let i = 0; i < formFields.length; i++) {
            let formField = formFields[i];
            let field = formField.dataset.field;
            if (!["day", "month", "year"].includes(field) && car[field]) {
                let value = car[field];
                if (value == "full_date") {
                    value += " 00:00:00";
                }

                if (value !== formField.value) {               
                    confirmText += page.formatLabel(field) + "\n";
                }
            }
        }

        if (confirm(confirmText)) {
            submitForm("/car-submit-update");
        }
    };

    const style = () => {
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
    };

    const parseToken = () => {
        let tokenInput = document.getElementById("car-create-token");
        return {
            type: "token",
            value: tokenInput.value + ""
        }
    };

    let settings = { 
        formId: "car-create-form", 
        overides: { "car-create-token": parseToken }, 
        dontParse: [] 
    };

    return CarCreateModule;
})();