let parser;

window.onload = () => {
    let parser = new FormParser(FORM_ID);
    parser.registerHandlers(HANDLERS);

    document.addEventListener('input', (e) => {
        let response = FormSubmission.send(parser.toJson(parser.conditions()), URL);
        response.then(data => {
            let container = document.getElementById(RESULTS_ID);
            container.innerHTML = data;
        });
    });

    // parser.registerHandler("INPUT", (formField, value, op = "LIKE") => {
    //     let searchTerms = FormParser.createTerms(value);
    //     let conditions = [];

    //     /////////////// Version 1.0 - can only search on one field
    //     searchTerms.forEach(term => {
    //         conditions.push(FormParser.createCondition('summary', term, op));
    //     });

    //     /////////////// Version 2.0 - can search on multiple fields (query builder must support OR conditions)
    //     // searchTerms.forEach(term => {
    //     //     conditions.push(...SEARCH_FIELDS.map(searchField => FormParser.createCondition(searchField, term, op)));
    //     // });
    //     return conditions;
    // });

    // parser.registerHandler("SELECT", (formField, value, op = "=") => {
    //     return Array(FormParser.createCondition(SELECT_LISTS[formField], value, op));
    // });
};


