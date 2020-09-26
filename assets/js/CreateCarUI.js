'use strict'

class CreateCarUI {
    constructor(props) {
        this.id = props.id;
        this.newFields = props.newFields;
        this.existingFields = props.existingFields;
        this.isUpdate = props.isUpdate;
        //this.car = props.car;
    }

    render() {
        let visibleFieldNames = this.newFields;
        let existingFieldNames = this.existingFields;
        let hiddenFieldNames = ["day", "month", "year"];

        let fieldOrder = ["title", "full_date", "subject_1", "subject_2", "summary", "result", "plaintiff", "defendant", "citation", "circut", "majority", "judges", "url"];

        let visibleFields = visibleFieldNames.map(field => {
            let id = "insert-" + field;
            let tag = "input";
            if (field == "summary" || field == "result") {
                tag = "textarea";
            }

            return vNode(
                "div",
                { id: field, class: "form-field" },
                [
                    vNode(
                    "label",
                    { id: id + "-label", for: id },
                    formatLabel(field) + ": "
                    ),
                    vNode(
                        tag,
                        { id: id, type: field == "full_date" ? "date" : "text", class: "car-create-field", "data-field": field, "data-row-id": 1, rows: 5 },
                        []
                    )
                ]
            );
        });

        for (let field in existingFieldNames) {
            let lookupProps = {
                lookup: {
                    className: "form-field"
                },
                input: {
                    className: "car-create-field",
                    "data-field": field, 
                    "data-row-id": 1
                }
            }

            let lookupElement = new LookupElement(field, existingFieldNames[field], lookupProps);
            visibleFields.push(lookupElement.render());
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

        let visibleFieldsVNode = vNode(
            "div",
            { id: "form-fields" },
            sortedVisibleFields
        );     

        let hiddenFields = hiddenFieldNames.map(field => {
            return vNode(
                "input",
                { type: "hidden", id: "insert-" + field, class: "car-create-field", "data-field": field, "data-row-id": 1 },
                []
            );
        });

        hiddenFields.push(vNode(
            "input",
            {type: "hidden", id: "insert-id", class: "car-create-field", "data-field": "id"},
            []
        ));

        let hiddenFieldsVNode = vNode(
            "div",
            { id: "form-hidden-fields" },
            hiddenFields
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

        /*let tokenVNode = super.createVNode(
            "input",
            { type: "hidden", id: "car-create-token", value: token },
            [],
            this
        );*/

        let resultsVNode = vNode(
            "div",
            { id: "car-create-results" },
            []
        );

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

        let searchBoxVNode = new SearchBoxElement("test", "solo");

        let formVNode = vNode(
            "form",
            { id: this.id },
            [resultsVNode, visibleFieldsVNode, hiddenFieldsVNode, carsLinkVNode, buttonVNode, sandboxVNode.render(), sandboxVNode2.render(), sandboxVNode3.render(), sandboxVNode4.render(), sandboxVNode5.render(), searchBoxVNode.render()]
        );

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
        //let inputs = document.getElementsByTagName("INPUT");
        //let textareas = document.getElementsByTagName("TEXTAREA");
        let formFields = document.getElementsByClassName("car-create-field");
        //formFields.push(...inputs);
        //formFields.push(...textareas);
        
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