'use strict'

class FormParserComponent extends BaseComponent {
    
    constructor(settings) {
        super(settings);

        // this.listen = ["input"]; // OLD WAY TO LISTEN FOR EVENTS

        this.id = settings.FORM_ID;
        this.url = settings.URL;
        this.resultsId = settings.RESULTS_ID;

        this.handlers = {};

        this.registerHandlers(settings.HANDLERS);
    }

    attachAttributes() {
        for(let i = 0; i < this.form.elements.length; i++) {
            let a = document.createAttribute("data-form-id");
            a.value = this.id;
            this.form.elements[i].setAttributeNode(a);
        }
    }

    render() {
        let headingVNode = super.createVNode(
            "h2",
            {},
            "OCDLA Criminal Apellate Review Search",
            this
        );

        let selectOptions = options.map(option => {
            return super.createVNode(
                "option",
                { value: option },
                option,
                this
            );
        });

        let selectVNode = super.createVNode(
            "select",
            { id: "car-subject_1", oninput: "handleEvent" },
            selectOptions,
            this
        );

        var inputVNode = super.createVNode(
            "input",
            { id: "car-search-box", placeholder: "Search case reviews", oninput: "input", onclick: "click" }, 
            [], 
            this
        );

        let formVNode = super.createVNode(
            "form",
            { id: "car-form" },
            [headingVNode, selectVNode, inputVNode],
            this
        );

        var formElement = super.createElement(formVNode);
        
        document.getElementById('stage-content').prepend(formElement);

        this.form = document.getElementById(this.id); // used by component
        this.root = document.getElementById(this.id); // used by event framework

        this.attachAttributes();

        // this.listen.forEach(event => {      
        //     EventFramework.registerEventListener(event, this, this.id);     // OLD WAY TO LISTEN FOR EVENTS
        // });
    }

    click(e) {
        console.log('Click event on ' + e.target.id);
    }

    input(e) {
        let response = FormSubmission.send(this.toJson(this.conditions()), this.url);
        response.then(data => {
            let container = document.getElementById(this.resultsId);
            container.innerHTML = data;
        });
    }

    getHandler(name) {
        return this.handlers[name];
    }

    registerHandler(tagName, method) {
        this.handlers[tagName] = method;
    }

    registerHandlers(handlers) {
        handlers.forEach(handler => {
            this.registerHandler(handler.tagName, handler.method);
        });
    }

    elements(elementId) {
        return !!elementId ? document.getElementById(elementId) : Array.from(this.form.elements);
    }

    values(elementId) {
        if(elementId) {
            return { elementId: { "value":this.elements(elementId).value, "tagName":element.tagName }};
        }
        let allValues = {};
        this.elements().forEach(element => {  
            allValues[element.id] = { "value": element.value, "tagName":element.tagName };
        });
        return allValues;
    }
    
    conditions() {
        let conditions = [];
        let formData = this.values();
        for(let formField in formData) {
            let data = formData[formField];
            if(this.getHandler(data.tagName)) {
                let handler = this.getHandler(data.tagName);
                conditions.push(...handler(formField, formData[formField].value));
            }
        }
        return conditions;
    }

    toJson(value) {
        return JSON.stringify(value);
    }

    handleEvent(e) {
        let target = e.target;

        if(e.type == "input"){
            this.input(e);
        }

        if(e.type == "click"){
            this.click(e);
        }

        return false;
    }

    // createCondition(field, value, op) { // should move to a DbQuery static class
    //     return {"field":field, "op":op, "value":value};
    // };

    // createTerms(value){
    //     let punctuationless = value.replace(/[\.,-\/#!$%\^&\*;:{}=\-_`~()@\+\?><\[\]\+]/g, ' '); // could cause problems with dates
    //     let extraSpaceRemoved = punctuationless.replace(/ +(?= )/g,'');
    //     return extraSpaceRemoved.split(' ').filter(Boolean);
    // };
}


// const FormParserComponent = (function(){

//     let formParserComponent = {
//         // Render should do 3 things:
//         // generate HTML string (or HTML like structure), declare local styles, specify events that it is interested in
//         render: function() {
//             var inputVNode = vNodeFramework.createVNode(
//                 "input",
//                 { id: "car-search-box", placeholder: "Search case reviews", oninput: "handleEvent", onclick: "click" }, 
//                 [], 
//                 this
//             );
            
//             var inputElement = vNodeFramework.createElement(inputVNode);
            
//             console.log(inputElement);

//             this.form.appendChild(inputElement);



//             // this.listen.forEach(event => {
//             //     EventFramework.registerEventListener(event, this, this.id);
//             // });
//         },

//         click: function(e) {
//             alert('Hello World');
//         },

//         input: function(e) {
//             let response = FormSubmission.send(this.toJson(this.conditions()), this.url);
//             response.then(data => {
//                 let container = document.getElementById(this.resultsId);
//                 container.innerHTML = data;
//             });
//         },

//         getHandler: function(name) {
//             return this.handlers[name];
//         },

//         registerHandler: function(tagName, method) {
//             this.handlers[tagName] = method;
//         },

//         registerHandlers: function(handlers) {
//             handlers.forEach(handler => {
//                 this.registerHandler(handler.tagName, handler.method);
//             });
//         },

//         elements: function(elementId) {
//             return !!elementId ? document.getElementById(elementId) : Array.from(this.form.elements);
//         },

//         values: function(elementId) {
//             if(elementId) {
//                 return { elementId: { "value":this.elements(elementId).value, "tagName":element.tagName }};
//             }
//             let allValues = {};
//             this.elements().forEach(element => {  
//                 allValues[element.id] = { "value": element.value, "tagName":element.tagName };
//             });
//             return allValues;
//         },
        
//         conditions: function() {
//             let conditions = [];
//             let formData = this.values();
//             for(let formField in formData) {
//                 let data = formData[formField];
//                 if(this.getHandler(data.tagName)) {
//                     let handler = this.getHandler(data.tagName);
//                     conditions.push(...handler(formField, formData[formField].value));
//                 }
//             }
//             return conditions;
//         },

//         toJson: function(value) {
//             return JSON.stringify(value);
//         },

//         handleEvent: function(e) {
//             let target = e.target;

//             if(e.type == "input"){
//                 this.input(e);
//             }
//             return false;
//         },


//         listen: ["input"]
//     };

//     function FormParserComponent(settings) {
//         this.id = settings.FORM_ID;
//         this.url = settings.URL;
//         this.resultsId = settings.RESULTS_ID;
//         this.form = document.getElementById(this.id);
//         this.root = document.getElementById(this.id);
//         this.handlers = {};
//         for(let i = 0; i < this.form.elements.length; i++) {
//             let a = document.createAttribute("data-form-id");
//             a.value = this.id;
//             this.form.elements[i].setAttributeNode(a);
//         }
//         this.registerHandlers(settings.HANDLERS);
//     }

//     FormParserComponent.createCondition = function(field, value, op) { // should move to a DbQuery static class
//         return {"field":field, "op":op, "value":value};
//     };

//     FormParserComponent.createTerms = (value) => {
//         let punctuationless = value.replace(/[\.,-\/#!$%\^&\*;:{}=\-_`~()@\+\?><\[\]\+]/g, ' '); // could cause problems with dates
//         let extraSpaceRemoved = punctuationless.replace(/ +(?= )/g,'');
//         return extraSpaceRemoved.split(' ').filter(Boolean);
//     };

//     FormParserComponent.prototype = formParserComponent;
    

//     return FormParserComponent;
// })();