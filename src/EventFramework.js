const EventFramework = (function() {

    function EventFramework() {}

    EventFramework.handlers = {};

    EventFramework.init = function() {

    };

    EventFramework.dump = function() {
        console.log(EventFramework.handlers);
    };

    EventFramework.registerEventListener = function(type, component) {
        let exists = false;

        for(let index in EventFramework.handlers[type]) {
            let handler = EventFramework.handlers[type][index];
            if (handler.id == component.id) exists = true;
        }

        if (!exists) {       
            if(!EventFramework.handlers[type]) {
                EventFramework.handlers[type] = [];

                document.addEventListener(type, EventFramework.handle);
            }

            EventFramework.handlers[type].push(component);
        }
    };

    EventFramework.handle = function(e) {
        let target = e.target;
        let type = e.type;

        EventFramework.handlers[type].forEach(component => {
            if(EventFramework.owns(target, component)) {
                component.parser.handleEvent(e);
            }
        });

    };

    EventFramework.owns = function(element, component) {
        return component.root.contains(element);
    };

     return EventFramework;
})();


function example() {
    handlers = {
        input: [],
        click: []
    };
}