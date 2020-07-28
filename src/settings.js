const SEARCH_WIDGET_SETTINGS = {
    FORM_ID: "car-form",

    URL: "/car-results",
    
    RESULTS_ID: "car-results",
    
    SELECT_LISTS: {
        "car-subject_1": "subject_1",
        "car-dates": "datediff(curdate(), full_date)"
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
                    conditions.push(DBQuery.createCondition(formField, term, op));
                });
        
                /////////////// Version 2.0 - can search on multiple fields (query builder must support OR conditions)
                // searchTerms.forEach(term => {
                //     conditions.push(...SEARCH_FIELDS.map(searchField => DBQuery.createCondition(searchField, term, op)));
                // });
                return conditions;
            }
        },
        {
            "tagName": "LIMIT_INPUT",
            "method": (rowCount, offset = 0) => {
                return Array(DBQuery.createLimitCondition(rowCount, offset));
            }
        },
        {
            "tagName": "SELECT",
            "method": (formField, value, op = "=") => {
                return Array(DBQuery.createCondition(SEARCH_WIDGET_SETTINGS.SELECT_LISTS[formField], value, op));
            }
        },
        {
            "tagName": "SORT_SELECT",
            "method": (formField, desc = false) => {
                return Array(DBQuery.createSortCondition(formField, desc));
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