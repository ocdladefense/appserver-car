const FormParser = (function(){

    let formParser = {
        getHandler: function(name) {
            return this.handlers[name];
        },

        registerHandler: function (tagName, method) {
            this.handlers[tagName] = method;
        },

        registerHandlers: function(handlers) {
            handlers.forEach(handler => {
                this.registerHandler(handler.tagName, handler.method);
            });
        },

        elements: function(elementId) {
            return !!elementId ? document.getElementById(elementId) : Array.from(this.form.elements);
        },

        values: function(elementId) {
            if(elementId) {
                return { elementId: { "value":this.elements(elementId).value, "tagName":element.tagName }};
            }
            let allValues = {};
            this.elements().forEach(element => {  
                allValues[element.id] = { "value": element.value, "tagName":element.tagName };
            });
            return allValues;
        },
        
        conditions: function() {
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
        },

        toJson: function(value) {
            return JSON.stringify(value);
        }

        // processInput: () => {
        //     fetch("/car-results", {
        //         method:"post",
        //         body: JSON.stringify(this.conditions())
        //     })
        //     .then(results => results.text())
        //     .then(data => {
        //         let container = document.getElementById("car-results");
        //         container.innerHTML = data;
        //     });
        // }
    };

    function FormParser(formId) {
        this.form = document.getElementById(formId);
        this.handlers = {};
    }

    FormParser.createCondition = function(field, value, op) {
        return {"field":field, "op":op, "value":value};
    };

    FormParser.createTerms = (value) => {
        let punctuationless = value.replace(/[\.,-\/#!$%\^&\*;:{}=\-_`~()@\+\?><\[\]\+]/g, ' '); // could cause problems with dates
        let extraSpaceRemoved = punctuationless.replace(/ +(?= )/g,'');
        return extraSpaceRemoved.split(' ').filter(Boolean);
    };

    FormParser.prototype = formParser;

    return FormParser;
})();