'use strict'

class CreateCarUI extends BaseComponent {
    constructor() {
        super();

        this.id = "car-create-form";
    }

    render() {
        let visibleFieldNames = newFields;
        let existingFieldNames = existingFields;
        let hiddenFieldNames = ["day", "month", "year"];

        let fieldOrder = ["title", "full_date", "subject_1", "subject_2", "summary", "result", "plaintiff", "defendant", "citation", "circut", "majority", "judges", "url"];

        let visibleFields = visibleFieldNames.map(field => {
            let id = "insert-" + field;
            let tag = "input";
            if (field == "summary" || field == "result") {
                tag = "textarea";
            }

            return super.createVNode(
                "div",
                { id: field, class: "form-field" },
                [
                    super.createVNode(
                    "label",
                    { id: id + "-label", for: id },
                    this.formatLabel(field) + ": ",
                    this
                    ),
                    super.createVNode(
                        tag,
                        { id: id, type: field == "full_date" ? "date" : "text", class: "car-create-field", "data-field": field, "data-row-id": 1, rows: 5 },
                        [],
                        this
                    )
                ],
                this
            );
        });

        for (let field in existingFieldNames) {
            visibleFields.push(this.existingOptionVNode(field, existingFieldNames[field]));
        }
        
        let sortedVisibleFields = fieldOrder.map(field => {
            for (let i = 0; i < visibleFields.length; i++) {
                let node = visibleFields[i];
                let nodeField = node.props.id;
                if (nodeField == field) {
                    return node;
                }
            }
        });

        let visibleFieldsVNode = super.createVNode(
            "div",
            { id: "form-fields" },
            sortedVisibleFields,
            this
        );     

        let hiddenFields = hiddenFieldNames.map(field => {
            return super.createVNode(
                "input",
                { type: "hidden", id: "insert-" + field, class: "car-create-field", "data-field": field, "data-row-id": 1 },
                [],
                this
            );
        });

        if (isUpdate) {
            hiddenFields.push(super.createVNode(
                "input",
                {type: "hidden", id: "insert-id", class: "car-create-field", "data-field": "id"},
                [],
                this
            ));
        }

        let hiddenFieldsVNode = super.createVNode(
            "div",
            { id: "form-hidden-fields" },
            hiddenFields,
            this
        );

        let buttonVNode = super.createVNode(
            "button",
            { type: "button", id: "car-submit-button" },
            "Submit",
            this
        );

        let carsLinkVNode = super.createVNode(
            "a",
            { href: "../cars" },
            "Back",
            this
        );

        /*let tokenVNode = super.createVNode(
            "input",
            { type: "hidden", id: "car-create-token", value: token },
            [],
            this
        );*/

        let formVNode = super.createVNode(
            "form",
            { id: this.id },
            [visibleFieldsVNode, hiddenFieldsVNode, buttonVNode, carsLinkVNode],
            this
        );

        let formElement = super.createElement(formVNode);
        
        document.getElementById("car-create-content").prepend(formElement);

        this.form = document.getElementById(this.id);

        this.attachSelectEvents();

        if (isUpdate) {
            this.fillUpdateFields();
        }
    }

    existingOptionVNode(field, values) {
        let options = values.map(value => {
            return super.createVNode(
                "option",
                { value: value },
                value,
                this
            );
        });

        options.unshift(
            super.createVNode(
                "option",
                { value: "NEW" },
                "--NEW--",
                this
            )
        );

        let selectVNode = super.createVNode(
            "select",
            { id: field + "-select", class: "existing-select" },
            options,
            this
        );

        let inputVNode = super.createVNode(
            "input",
            { id: "insert-" + field, class: "existing-input car-create-field", "data-field": field, "data-row-id": 1 },
            [],
            this
        );

        let divVNode = super.createVNode(
            "div",
            { id: field + "-group", class: "existing-group" },
            [selectVNode, inputVNode],
            this
        );

        let labelVNode = super.createVNode(
            "label",
            { id: "insert-" + field + "-label" },
            this.formatLabel(field) + ": ",
            this
        );

        return super.createVNode(
            "div",
            { id: field, class: "form-field" },
            [labelVNode, divVNode],
            this
        );
    }

    formatLabel(field) {
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

    attachSelectEvents() {
        let selects = document.getElementsByTagName("SELECT");
        for (let i = 0; i < selects.length; i++) {
            let select = selects[i];
            select.addEventListener("input", this.handleExistingOption);
        }
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
        let selects = document.getElementsByTagName("SELECT");
        for (let i = 0; i < selects.length; i++) {
            let select = selects[i];
            let input = select.parentNode.getElementsByTagName("INPUT")[0];

            if (select.value != "NEW") {
                input.value = select.value;
            }
        }
    }

    fillDateFields() {
        let date = document.getElementById("insert-full_date").value;
        let [year, month, day] = date.split("-");
        month -= 1;
        //let today = new Date(year, month, day);
        //let day = today.getDate();
        var monthName = [ "January", "February", "March", "April", "May", "June", 
           "July", "August", "September", "October", "November", "December" ];
        //let month = today.toLocaleString('default', { month: 'long' });
        //let year = today.getFullYear();
        document.getElementById("insert-full_date").value = date;//(year + "/" + month + "/" + day).toISOString().slice(0, 19).replace('T', ' '); //Formating for MySQL
        document.getElementById("insert-day").value = day;
        document.getElementById("insert-month").value = monthName[month];
        document.getElementById("insert-year").value = year;
    }

    onFormSubmit(fn) {    
        let thisContext = this;

        function theHandler() {
            thisContext.selectExistingOptionFields();

            if (thisContext.validateForm()) {
                //let date = document.getElementById("insert-full_date").value;
                //let [year, month, day] = date.split("-");
                //month -= 1;
                thisContext.fillDateFields();
                fn();
            }           
        }
        
        document.getElementById("car-submit-button").addEventListener("click", theHandler);
    }

    validateForm() {
        this.clearErrors();

        let formFields = document.getElementsByClassName("car-create-field");
        //formFields.push(...this.form.getElementsByTagName("input"));
        //formFields.push(...this.form.getElementsByTagName("textarea"));

        let errors = [];

        for (let i = 0; i < formFields.length; i++) {
            let formField = formFields[i];
            if (["day", "month", "year"].includes(formField.dataset.field)) {
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
            window.scrollTo(0, 0);
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
            return super.createVNode(
                "li",
                { class: "errors" },
                this.formatLabel(error) + " is required.",
                this
            );
        });

        let errorListVNode = super.createVNode(
            "ul",
            { id: "form-errors" },
            errorItems,
            this
        );

        let formElement = super.createElement(errorListVNode);
        
        document.getElementById(this.id).prepend(formElement);
    }

    fillUpdateFields() {
        //let inputs = document.getElementsByTagName("INPUT");
        //let textareas = document.getElementsByTagName("TEXTAREA");
        let formFields = document.getElementsByClassName("car-create-field");
        //formFields.push(...inputs);
        //formFields.push(...textareas);
        
        for (let i = 0; i < formFields.length; i++) {
            let formField = formFields[i];
            let field = formField.dataset.field;
            if (car[field]) {
                if (existingFields[field]) {
                    document.getElementById(field + "-select").value = car[field];
                    formField.disabled = true;
                } else {
                    formField.value = car[field];
                }
            }
        }
    }
}