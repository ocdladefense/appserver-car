const myQuery = {
    name: "foobar",
    time: null,
    type: "select",
    conditions: [ {
        field: "subject",
        op: "=",
        value: "evidence"
    }],
    updates: [
        {
            field: "",
            value: ""
        }
    ],
    options: {
        limit: 0,
        offset: 0,
        orderBy: {
            field: "",
            desc: true
        }
    },

};



[{"type":"condition","field":"subject_1","op":"=","value":"ALL"},
{"type":"condition","field":"datediff(curdate(), full_date)","op":"<","value":"ALL"},
{"type":"sortCondition","field":"title","desc":"false"},
{"type":"limitCondition","rowCount":10,"offset":0}]