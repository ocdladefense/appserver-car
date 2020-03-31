const SEARCH_WIDGET_SETTINGS = {
    FORM_ID: "car-form",

    URL: "/car-results",
    
    RESULTS_ID: "car-results",
    
    SELECT_LISTS: {
        'car-subject_1': 'subject_1'
    },
    
    SEARCH_FIELDS: ['subject_1', 'summary'],
    
    HANDLERS: [
        {
            "tagName": "INPUT",
            "method": (formField, value, op = "LIKE") => {
                let searchTerms = DBQuery.createTerms(value);
                let conditions = [];
        
                /////////////// Version 1.0 - can only search on one field
                searchTerms.forEach(term => {
                    conditions.push(DBQuery.createCondition('summary', term, op));
                });
        
                /////////////// Version 2.0 - can search on multiple fields (query builder must support OR conditions)
                // searchTerms.forEach(term => {
                //     conditions.push(...SEARCH_FIELDS.map(searchField => DBQuery.createCondition(searchField, term, op)));
                // });
                return conditions;
            }
        },
        {
            "tagName": "SELECT",
            "method": (formField, value, op = "=") => {
                return Array(DBQuery.createCondition(SEARCH_WIDGET_SETTINGS.SELECT_LISTS[formField], value, op));
            }
        }
    ]
}




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