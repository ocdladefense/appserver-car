'use strict'

class CreateCarUI {
    constructor(props) {
        this.id = props.id;
        this.newFields = props.newFields;
        this.existingFields = props.existingFields;
        this.components = [];
    }

    render() {
        let visibleFieldNames = this.newFields;

        let existingFieldNames = this.existingFields;
        let fieldsFromExistingFields = [];
        for (let eField in existingFieldNames) {
            fieldsFromExistingFields.push(eField);
        }

        let hiddenFieldNames = ["day", "month", "year"];

        let fieldOrder = ["title", "full_date", "subject_1", "subject_2", "summary", "result", "plaintiff", "defendant", "citation", "circut", "majority", "judges", "url"];
        fieldOrder.push(...hiddenFieldNames);

        let resultsVNode = vNode(
            "div",
            { id: "car-create-results" },
            []
        );

        let idVNode = vNode(
            "input",
            {type: "hidden", id: "id-input", class: "car-create-field", "data-field": "id"},
            []
        );

        let formFields = [];

        for (let i in fieldOrder) {
            let field = fieldOrder[i];
            let formField = {
                field: field,
                label: formatLabel(field) + ":",
                props: {
                    textInput: { className: "form-field" },
                    lookup: { className: "form-field" },
                    input: { className: "car-create-field" }
                }
            };

            if (visibleFieldNames.includes(field)) { //TextInputElement
                if (["summary", "result"].includes(field)) { //TextInputElement of type textarea
                    formField.type = "textinput-textarea";
                    formField.props.input.rows = 5;
                } else if (field === "full_date") { //TextInputElement of type input that needs a type attribute of "date"
                    formField.props.input.type = "date";
                }
            } else if (fieldsFromExistingFields.includes(field)) { //LookupElement
                formField.type = "lookup";
                formField.values = existingFieldNames[field];
            } else if (hiddenFieldNames.includes(field)) { //HiddenElement
                formField.type = "hidden";
            }

            formFields.push(formField);
        }

        let formCom = new InsertForm(this.id, formFields);
        let formVNode = formCom.render();

        formVNode.children.unshift(resultsVNode, idVNode);
        
        let carsLinkVNode = vNode(
            "a",
            { id: "car-create-cancel" },
            [
                vNode(
                    "span",
                    {},
                    "Cancel"
                )
            ]
        );

        let buttonVNode = vNode(
            "a",
            { id: "car-submit-button" },
            [
                vNode(
                    "span",
                    {},
                    "Submit Changes"
                )
            ]
        );

        formVNode.children.push(carsLinkVNode, buttonVNode);

        return formVNode;
    }

    renderMore() {
        this.attachSelectEvents();

        this.styleForm();
    }

    attachSelectEvents() {
        let selects = document.getElementById(this.id).getElementsByTagName("SELECT");
        for (let i = 0; i < selects.length; i++) {
            let select = selects[i];
            select.addEventListener("input", this.handleExistingOption);
        }

        /*for (let i in this.components) {
            let component = this.components[i];
            component.attachEventListeners();
        }*/
    }

    handleExistingOption(e) {
        let select = e.target;
        let input = select.parentNode.getElementsByTagName("INPUT")[0];
        input.value = "";
        if (select.value == "NEW") {
            input.disabled = false;
        } else {
            input.disabled = true;
        }
    }

    selectExistingOptionFields() {
        let selects = document.getElementById(this.id).getElementsByTagName("SELECT");
        for (let i = 0; i < selects.length; i++) {
            let select = selects[i];
            let input = select.parentNode.getElementsByTagName("INPUT")[0];

            if (select.value != "NEW") {
                input.value = select.value;
            }
        }
    }

    fillDateFields() {
        let date = document.getElementById("full_date-input").value;
        let [year, month, day] = date.split("-");
        month -= 1;
        var monthName = [ "January", "February", "March", "April", "May", "June", 
           "July", "August", "September", "October", "November", "December" ];
        document.getElementById("full_date-input").value = date;
        document.getElementById("day-input").value = day;
        document.getElementById("month-input").value = monthName[month];
        document.getElementById("year-input").value = year;
    }

    onFormSubmit(fn) {    
        let thisContext = this;

        function theHandler() {
            thisContext.selectExistingOptionFields();

            if (thisContext.validateForm()) {
                thisContext.fillDateFields();
                fn();
            }           
        }
        
        document.getElementById("car-submit-button").addEventListener("click", theHandler);
    }

    validateForm() {
        this.clearErrors();

        let formFields = document.getElementsByClassName("car-create-field");

        let errors = [];

        for (let i = 0; i < formFields.length; i++) {
            let formField = formFields[i];
            if (["day", "month", "year", "id"].includes(formField.dataset.field)) {
                continue;
            }

            if (formField.value == null || formField.value.trim() == "") {
                errors.push(formField.dataset.field);
            }
        }

        if (errors.length <= 0) {
            return true;
        } else {
            this.addErrors(errors);
            document.getElementById("modal").scrollTo(0, 0);
            return false;
        }
    }

    clearErrors() {
        if (document.getElementById("form-errors")) {
            document.getElementById("form-errors").remove();
        }
    }

    addErrors(errors) {
        let errorItems = errors.map(error => {
            return vNode(
                "li",
                { class: "errors" },
                formatLabel(error) + " is required."
            );
        });

        let errorListVNode = vNode(
            "ul",
            { id: "form-errors" },
            errorItems
        );

        let formElement = createElement(errorListVNode);
        
        document.getElementById(this.id).prepend(formElement);
    }

    populate(car) {
        let formFields = document.getElementsByClassName("car-create-field");
        
        for (let i = 0; i < formFields.length; i++) {
            let formField = formFields[i];
            let field = formField.dataset.field;
            if (car[field]) {
                let value = car[field];
                if (field == "full_date") {
                    value = value.split(" ")[0];
                }
                if (this.existingFields[field]) {
                    document.getElementById(field + "-select").value = value;
                    formField.disabled = true;
                } else {
                    formField.value = value;
                }
            }
        }
    }

    styleForm(windowWidth) {
        let resetHeight = window.innerWidth < windowWidth;

        let fields = document.getElementsByClassName("form-field");
        for (let i = 0; i < fields.length; i++) {
            let field = fields[i];
            let children = field.childNodes;
            if (children[1].tagName != "TEXTAREA") {
                continue;
            }

            if (resetHeight) {
                children[0].style.height = "auto";
                continue;
            }

            let textarea = children[1];
            let inputHeight = textarea.offsetHeight + "px";
            children[0].style.height = inputHeight;
        }
    };
}

function exampleComponents() {
    let values1 = "The Only One";

    let values2 = ["first", "second", "third"];

    let values3 = [
        {"First": 1},
        {"Second": 2},
        {"Third": 3}
    ];
    
    let values4 =[
        {"First": 1},
        "Second",
        3
    ];

    let values5 = {
        id: "special-option",
        value: "star",
        text: "star power!!"
    };

    let sandboxVNode = new SelectElement("test1", values1);
    let sandboxVNode2 = new SelectElement("test2", values2);
    let sandboxVNode3 = new SelectElement("test3", values3);
    let sandboxVNode4 = new SelectElement("test4", values4);
    let sandboxVNode5 = new SelectElement("test5", values5);

    let searchBoxVNode = new SearchBoxElement("test", [1,2,3]);

    let textInputVNode = new TextInputElement("test", "Some Stuff", {}, "textarea");

    let lookupVNode = new LookupElement("test", ["one", 2]);

    return vNode(
        "div",
        {},
        [sandboxVNode.render(), sandboxVNode2.render(), sandboxVNode3.render(), sandboxVNode4.render(), sandboxVNode5.render(),
        searchBoxVNode.render(), textInputVNode.render(), lookupVNode.render()]
    );
}