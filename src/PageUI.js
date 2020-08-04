'use strict'

class PageUI extends BaseComponent {
    constructor() {
        super();

        this.id = "car-form";

        this.timer;
        this.timerLength = 500;
    }

    attachAttributes() {
        for(let i = 0; i < this.form.elements.length; i++) {
            let elem = this.form.elements[i];
            let a = document.createAttribute("data-form-id");
            a.value = this.id;
            elem.setAttributeNode(a);
        }
    }

    attachSelectDataAttributes(target) {
        if (target.tagName == "SELECT") {       
            let option = target.options[target.selectedIndex];
            for (let i in option.attributes) {
                let att = option.attributes[i];
                if (att.name && att.name.startsWith("data-")) {
                    if (target[att.name]) {
                        target[att.name].value = att.value;
                    } else {
                        target.setAttribute(att.name, att.value);
                    }
                }
            }
        }
    }

    readMoreClick(e) {
        let target = e.target;
        let parent = target.parentNode;
    
        if (!target.classList.contains("readMoreButton")) {
            return;
        }
    
        let ellipsis = parent.querySelector(".ellipsis");
        let moreText = parent.querySelector(".more");
        let btnText = parent.querySelector(".readMoreButton");
    
        readMore(ellipsis, moreText, btnText);
    }

    handleButton() {
        let form = document.getElementById("car-form");
        let button = document.getElementById("car-form-button");
        if (button.value == "Show Form") {
            form.style.display = "block";
            button.value = "Hide Form";
        } else {
            form.style.display = "none";
            button.value = "Show Form";
        }
    }

    render() {
        let headingVNode = super.createVNode(
            "h2",
            {},
            "OCDLA Criminal Apellate Review Search",
            this
        );

        let selectOptions = subjects.options.map(option => {
            return super.createVNode(
                "option",
                { value: option.value },
                option.name.toLowerCase(),
                this
            );
        });

        let allOption = super.createVNode(
            "option",
            { value: "ALL" },
            "--ALL-- (Select Subject)",
            this
        );

        selectOptions.unshift(allOption);

        let selectVNode = super.createVNode(
            "select",
            { id: "car-subject_1", class: "car-form-field", "data-field": subjects.field },
            selectOptions,
            this
        );

        let dateOptions = dateRanges.options.map(option => {
            if (option.value == "space") {
                return super.createVNode(
                    "option",
                    { disabled: true },
                    option.name,
                    this
                );
            } else {
                return super.createVNode(
                    "option",
                    { value: option.value },
                    option.name,
                    this
                );
            }
        });

        let selectDateVNode = super.createVNode(
            "select",
            { id: "car-dates", class: "car-form-field", "data-field": dateRanges.field, "data-op": dateRanges.op },
            dateOptions,
            this
        );

        let searchCheckBoxes = searches.flatMap(checkBox => {
            return [super.createVNode(
                "label",
                { for: checkBox.name },
                checkBox.name,
                this
            ),
            super.createVNode(
                "input",
                { type: "checkbox", class: "search-checkbox", id: checkBox.name, value: checkBox.value },
                [],
                this
            )
            ];
        });

        let checkBoxesVNode = super.createVNode(
            "div",
            { id: "checkbox-group" },
            searchCheckBoxes,
            this
        );

        var inputVNode = super.createVNode(
            "input",
            { id: "car-search-box" }, 
            [], 
            this
        );

        let sortOptions = sorts.map(option => {
            return super.createVNode(
                "option",
                { value: option.value, "data-desc": option.desc },
                option.name,
                this
            );
        });

        let selectSortVNode = super.createVNode(
            "select",
            { id: "car-sort", class: "car-form-field", "data-desc": true},
            sortOptions,
            this
        );

        let formSearchVNode = super.createVNode(
            "div",
            { id: "car-search-container" },
            [checkBoxesVNode, inputVNode],//selectSearchLabelVNode, selectSearchVNode, inputVNode],
            this
        );

        let formFilterVNode = super.createVNode(
            "div",
            {},
            [selectVNode, selectDateVNode, selectSortVNode],
            this
        );
        /*
        let limitVNode = super.createVNode(
            "input",
            { id: "car-limit", class: "car-form-field" },
            [],
            this
        );

        let limitLabelVNode = super.createVNode(
            "label",
            { for: "car-limit" },
            "Number of CAR's to Return: ",
            this
        );*/

        let mobileSeparatorVNode = super.createVNode(
            "hr",
            { id: "car-mobile-separator" },
            [],
            this
        );

        let formVNode = super.createVNode(
            "form",
            { id: "car-form" },
            [formSearchVNode, mobileSeparatorVNode, formFilterVNode],
            this
        );

        let buttonVNode = super.createVNode(
            "input",
            { type: "button", id: "car-form-button", value: "Show Form" },
            [],
            this
        );

        let completeVNode = super.createVNode(
            "div",
            {},
            [headingVNode, buttonVNode, formVNode],
            this
        );

        var formElement = super.createElement(completeVNode);
        
        document.getElementById('stage-content').prepend(formElement);

        this.form = document.getElementById(this.id); // used by component

        this.attachAttributes();

        //Check the first checkbox
        (document.getElementsByClassName("search-checkbox"))[0].checked = true;

        document.getElementById("car-form-button").addEventListener("click", this.handleButton);
        
        searchPlaceholderText();
    }  
    
    addFeature(feature, callback) {
        switch (feature) {
            case "infiniteScroll":
                let infiniteScroller = callback;
                document.addEventListener('scroll', infiniteScroller.loadMoreResults);
                break;  
            case "readMoreSummary":
                document.addEventListener('click', callback);
                break;
            case "searchBoxPlaceholder":
                let checkboxes = document.getElementById("checkbox-group").childNodes;
                for (let i in checkboxes) {
                    let checkbox = checkboxes[i];
                    if (checkbox.type == "checkbox") {
                        checkbox.addEventListener("input", callback);
                    }
                }
                break;
        }
        
    }

    getCheckedCheckboxes() {
        let checkboxes = document.getElementsByClassName("search-checkbox");
        let checkedBoxes = [];
        for (let i in checkboxes) {
            if (checkboxes[i].checked) {
                checkedBoxes.push(checkboxes[i]);
            }
        }
        return checkedBoxes;
    }

    addSearchBoxValues() {
        if (!!document.getElementById("car-search-box-values")) {
            document.getElementById("car-search-box-values").remove();
        }
        let removeWheres = [];
        settings.whereFields.map((field) => {
            if (!field.startsWith("car-hiddenValue-")) {
                removeWheres.push(field);
            }
        });
        settings.whereFields = removeWheres;

        let checkedBoxes = this.getCheckedCheckboxes();
        let valueNodes = [];
        for (let i in checkedBoxes) {
            let checkbox = checkedBoxes[i];
            let id = "car-hiddenValue-" + checkbox.id;
            valueNodes.push(
                super.createVNode(
                    "input",
                    { id: id, style: "display: none;", value: JSON.stringify({
                        field: checkbox.value,
                        value: document.getElementById("car-search-box").value,
                        op: "LIKE"
                    }) },
                    [],
                    this
                )
            );
            settings.whereFields.push(id);
        }

        let searchBoxValueVNode = super.createVNode(
            "div",
            { id: "car-search-box-values" },
            valueNodes,
            this
        );

        let element = super.createElement(searchBoxValueVNode);
        document.getElementById("car-search-container").append(element);
    }

    handleCheckboxGroup() {
        if (target.parentNode.id == "checkbox-group") {
            document.getElementById("car-search-box").placeholder = searchPlaceholderText();
        }
    }

    onUserSearch(fn) {
        let thisContext = this;

        function theHandler(e, context = thisContext) {
            let target = e.target;

            const ignore = ["car-limit"];
            if (e.type != "input" || ignore.includes(target.id) || 
                (target.parentNode.id == "checkbox-group" && document.getElementById("car-search-box").value == "")) {
                return false;
            }   
           
            clearTimeout(context.timer);
            context.timer = setTimeout(() => {
                thisContext.attachSelectDataAttributes(target);

                scroller.reset();

                fn();
            }, context.timerLength);

            return false;
        }

        document.addEventListener("input", theHandler);
    };
}

function searchPlaceholderText() {
    let placeholder = "Search case reviews by ";

    let checkboxes = document.getElementsByClassName("search-checkbox");
    let searchBys = [];
    for(let i = 0; i < checkboxes.length; i++) {
        let checkbox = checkboxes[i];
        if (checkbox.checked) {
            searchBys.push(checkbox.value);
        }
    }

    for(let i = 0; i < searchBys.length; i++) {
        let searchBy = searchBys[i];
        if (i == searchBys.length - 1) {
            placeholder += searchBy;
        } else {
            placeholder += searchBy + " and ";
        }
    }

    document.getElementById("car-search-box").placeholder = placeholder;
}