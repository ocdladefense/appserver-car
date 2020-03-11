var searchForm = new CarForm("car-form");
var values = searchForm.values(); // returns one object, field:element_id value:value selected by user
var subjectList = searchForm.elements('subject_1');
var allElements = searchForm.elements();
var dataHandlers = {};
searchForm.registerHandler("INPUT", function(field, value, op){
    return this.createCondition(field, value, op);
});

function getHandler(handler) {
    return this.dataHandlers[handler];
}

subjectList.value();

searchForm.conditions();

searchForm.send();

function conditions() {
    var conditions = [];
    var formData = this.values();
    for(var field in formData) {
        var data = formData[field];
        if(getHandler(data.tagName)) {
            var handler = getHandler(data.tagName);
            conditions.push(handler(field, formData[field].value, op));
        }
        conditions.push(this.createCondition(field, value, op));
    }
    return conditions;
}

// submit button: submit.onsubmit = myCarForm.submit;
function submit() {
    try {
        var conditions = this.conditions();
        var requestBody = JSON.stringify(conditions);
        var data = this.elements('data');
        data.value = requestBody;
        return true;
    } catch(e) {
        window.alert(e.message); 
    }
}

function send() {
    var conditions = this.conditions();
    var requestBody = JSON.stringify(conditions);
    var request = fetch("/car-search", { body: "{\"foo\":\"bar\"}", method:"post" });
    request.then((results) => {
        console.log(results);
    });
}



const CarFormParser = (function(){

    let carFormParser = {
        carForm: () => { return document.getElementById("car-form"); },

        processInput: (e) => {
            let conditions = [];
            Array.from(CarFormParser.prototype.carForm().elements).forEach(element => {
                if(element.tagName == "INPUT") {

                    conditions.push(CarFormParser.prototype.createCondition(element.tagName, element.value));
                } else if (element.tagName == "SELECT") {
                    conditions.push(CarFormParser.prototype.createCondition(SELECT_LISTS.element.id, "=", element.value));
                }
            })
            console.log(conditions);
        },
        
        createCondition: (field, value, op) => {
            return {"field":field, "op":op, "value":value};
        }
    };

    function CarFormParser() {
        CarFormParser.prototype.carForm().addEventListener('input', this.processInput);
    }

    CarFormParser.prototype = carFormParser;

    return CarFormParser;
})();