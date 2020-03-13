const FORM_ID = "car-form";

const URL = "/car-results";

const RESULTS_ID = "car-results";

const SELECT_LISTS = {
    'car-subject_1': 'subject_1'
};

const SEARCH_FIELDS = ['subject_1', 'summary'];

const HANDLERS = [
    {
        "tagName": "INPUT",
        "method": (formField, value, op = "LIKE") => {
            let searchTerms = FormParser.createTerms(value);
            let conditions = [];
    
            /////////////// Version 1.0 - can only search on one field
            searchTerms.forEach(term => {
                conditions.push(FormParser.createCondition('summary', term, op));
            });
    
            /////////////// Version 2.0 - can search on multiple fields (query builder must support OR conditions)
            // searchTerms.forEach(term => {
            //     conditions.push(...SEARCH_FIELDS.map(searchField => FormParser.createCondition(searchField, term, op)));
            // });
            return conditions;
        }
    },
    {
        "tagName": "SELECT",
        "method": (formField, value, op = "=") => {
            return Array(FormParser.createCondition(SELECT_LISTS[formField], value, op));
        }
    }
];


let exampleValues = {
    "car-subject_1": {
        "value": "APPELLATE PROCEDURE",
        "tagName": "SELECT"
    },
    "car-search-box": {
        "value": "search term",
        "tagName": "INPUT"
    }
};