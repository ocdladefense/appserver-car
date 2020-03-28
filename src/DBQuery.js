const DBQuery = (function() {

    function DBQuery() { }

    DBQuery.createCondition = function(field, value, op) { 
        return {"field":field, "op":op, "value":value};
    };

    DBQuery.createTerms = function(value) {
        let punctuationless = value.replace(/[\.,-\/#!$%\^&\*;:{}=\-_`~()@\+\?><\[\]\+]/g, ' '); // could cause problems with dates
        let extraSpaceRemoved = punctuationless.replace(/ +(?= )/g,'');
        return extraSpaceRemoved.split(' ').filter(Boolean);
    };

    return DBQuery;

})();