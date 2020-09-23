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

    toggleForm() {
        let form = document.getElementById("car-form");
        let button = document.getElementById("car-form-button");
        if (button.value == "Show Search Form") {
            form.style.display = "block";
            button.value = "Hide Search Form";
        } else {
            form.style.display = "none";
            button.value = "Show Search Form";
        }
    }

    displayForm() {
        let form = document.getElementById("car-form");
        let button = document.getElementById("car-form-button");
        if (button.value == "Show Search Form") {
            form.style.display = "none";
        } else {
            form.style.display = "block";
        }
    }

    render() {
        let headingVNode = super.createVNode(
            "h2",
            {},
            "OCDLA Criminal Apellate Review Search",
            this
        );

        /*let selectOptions = [];
        
        if (subjects.options) {
            selectOptions = subjects.options.map(option => {
                return super.createVNode(
                    "option",
                    { value: option.value },
                    option.text.toLowerCase(),
                    this
                );
            });
        }

        let allOption = super.createVNode(
            "option",
            { value: "ALL" },
            "--ALL-- (Select Subject)",
            this
        );

        selectOptions.unshift(allOption);

        let selectVNode1 = super.createVNode(
            "select",
            { id: "car-subject_1", class: "car-form-field", "data-field": subjects.field },
            selectOptions,
            this
        );*/

        let selectOptions = subjects.options;
        selectOptions.unshift({text: "--ALL-- (Select Subject)", value: "ALL"});
        let selectSubject = new SelectElement("car-subject_1", selectOptions, {className: "car-form-field", "data-field": subjects.field});
        let selectVNode = selectSubject.render();

        /*let dateOptions = dateRanges.options.map(option => {
            if (option.value == "space") {
                return super.createVNode(
                    "option",
                    { disabled: true },
                    option.text,
                    this
                );
            } else {
                return super.createVNode(
                    "option",
                    { value: option.value },
                    option.text,
                    this
                );
            }
        });

        let selectDateVNode = super.createVNode(
            "select",
            { id: "car-dates", class: "car-form-field", "data-field": dateRanges.field, "data-op": dateRanges.op },
            dateOptions,
            this
        );*/

        let dateOptions = dateRanges.options;
        for (let i in dateOptions) {
            let date = dateOptions[i];
            if (date.value == "space") {
                date.disabled = true;
            }
        }
        let selectDate = new SelectElement("car-dates", dateRanges.options, {className: "car-form-field", "data-field": dateRanges.field, "data-op": dateRanges.op});
        let selectDateVNode = selectDate.render();

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

        /*let sortOptions = sorts.map(option => {
            return super.createVNode(
                "option",
                { value: option.value, "data-desc": option.desc },
                option.text,
                this
            );
        });

        let selectSortVNode = super.createVNode(
            "select",
            { id: "car-sort", class: "car-form-field", "data-desc": true},
            sortOptions,
            this
        );*/

        let sortOptions = sorts.map(option => {
            return {
                value: option.value,
                "data-desc": option.desc,
                text: option.text
            }
        });
        let selectSort = new SelectElement("car-sort", sortOptions, {className: "car-form-field", "data-desc": true});
        let selectSortVNode = selectSort.render();

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
            { id: this.id },
            [formSearchVNode, mobileSeparatorVNode, formFilterVNode],
            this
        );

        let buttonVNode = super.createVNode(
            "input",
            { type: "button", id: "car-form-button", value: "Show Search Form" },
            [],
            this
        );

        let carCreateLink = super.createVNode(
            "a",
            { id: "car-create-link", class: "car-link-btn" },
            [super.createVNode(
                "span",
                {},
                "Create New Criminal Apellate Review",
                this
            )],
            this
        );

        let completeVNode = super.createVNode(
            "div",
            {},
            [headingVNode, buttonVNode, formVNode, carCreateLink],
            this
        );

        var formElement = createElement(completeVNode);
        
        document.getElementById('stage-content').prepend(formElement);

        this.form = document.getElementById(this.id); // used by component

        this.attachAttributes();

        //Check the first checkbox
        (document.getElementsByClassName("search-checkbox"))[0].checked = true;

        document.getElementById("car-form-button").addEventListener("click", this.toggleForm);
        
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
            case "carCreate":
                document.getElementById("car-create-link").addEventListener("click", (e) => {
                    e.preventDefault();
                    callback();
                });
                break;
            case "carUpdate":
                let updateBtns = document.getElementsByClassName("car-update-link");
                for (let i = 0; i < updateBtns.length; i++) {
                    clearEventListeners(updateBtns[i]);
                    updateBtns[i].addEventListener("click", (e) => {
                        e.preventDefault();
                        callback(updateBtns[i].dataset.carid);
                    });
                }
                break;
            case "carDelete":
                let deleteBtns = document.getElementsByClassName("car-delete-link");
                for (let i = 0; i < deleteBtns.length; i++) {
                    clearEventListeners(deleteBtns[i]);
                    deleteBtns[i].addEventListener("click", (e) => {
                        e.preventDefault();
                        callback(deleteBtns[i].dataset.carid);
                    });
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

            const ignore = ["car-limit", "car-create-link"];
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

        this.form.addEventListener("input", theHandler);
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