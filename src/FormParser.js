//'use strict'

const FormParser = (function() {
    
    function FormParser() {}

    let id = "car-form";
    let resultsLimit;
    let resultsOffset = 0;

    const setResultsLimit = (limit) => {
        resultsLimit = limit;
    }

    const setResultsOffset = (offset) => {
        resultsOffset = offset;
    }

    const elements = (elementId) => {
        return !!elementId ? document.getElementById(elementId) : Array.from(document.getElementById(id).elements);
    };

    const values = (elementId) => {
        if(elementId) {
            return { elementId: { "value":elements(elementId).value, "tagName":element.tagName }};
        }
        let allValues = {};
        elements().forEach(element => {  
            allValues[element.id] = { "value": element.value, "tagName":element.tagName };
        });
        return allValues;
    };
    
    const parseConditions = () => {
        let conditions = [];

        const dontCondition = ["checkbox-group"];

        let formData = values();
        console.log(formData);
        for(let formField in formData) {
            let data = formData[formField];

            if (dontCondition.includes(formField) || dontCondition.includes((document.getElementById(formField)).parentNode.id) || 
                data.value == null || data.value.trim() == '' || typeof(data.value) == "undefined") {           
                continue;                          
            }

            let fieldParser = {
                "car-sort": parseSortSelect,
                "car-limit": parseLimitInput,
                "car-search-box": parseSearchBox,
                "car-dates": parseDateRange,
                "car-subject_1": parseSubjectSelect
            };
           
            conditions.push(fieldParser[formField](data));
        }

        //query = new DBQuery();
        //query.addCondition();
        conditions.push(DBQuery.createLimitCondition(resultsLimit, resultsOffset));

        return conditions;
    };

    const parseSortSelect = (data) => {
        let params = data.value.split("=");
        if (params.length == 1) {
            return DBQuery.createSortCondition(params[0]);
        } else {
            let desc = false;
            if (params[1].toUpperCase() == "DESC" || params[1].toLowerCase() == "true") {
                desc = true;
            }
            return DBQuery.createSortCondition(params[0], desc);
        }
    };

    const parseSubjectSelect = (data) => {
        return DBQuery.createCondition("subject_1", data.value);
    }

    const parseLimitInput = (data) => {
        return DBQuery.createLimitCondition(...data.value);
    };

    const parseSearchBox = (data) => {
        let checkboxes = document.getElementsByClassName("search-checkbox");
        let searchConditions = [];
        for(let i = 0; i < checkboxes.length; i++) {
            let checkbox = checkboxes[i];
            if (checkbox.checked) {
                let searchTerms = DBQuery.createTerms(data.value);
                let conditions = [];
        
                searchTerms.forEach(term => {
                    conditions.push(DBQuery.createCondition(checkbox.value, term, "LIKE"));
                });

                searchConditions.push(...conditions);
            }
        }
        
        if (searchConditions.length == 1) {
            return searchConditions[0];
        } else if (searchConditions.length > 1) {
            return searchConditions;
        }
    };

    const parseDateRange = function (data) {
        return DBQuery.createCondition("datediff(curdate(), full_date)", data.value, "<");
    };

    let proto = {
        parseConditions: parseConditions,
        parseLimitInput: parseLimitInput,
        setResultsLimit: setResultsLimit,
        setResultsOffset: setResultsOffset
    }

    FormParser.prototype = proto;

    return FormParser;
})();