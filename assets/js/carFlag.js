// When the checkbox is checked a function is called that will update the record via a fetch requesst.


function displayErrorPopUp(element, data){

    let modalMessage = data["error"];

    let modal = document.getElementById("error-pop-up-container") == null ? document.createElement("div") : document.getElementById("error-pop-up-container");
    modal.setAttribute("id", "error-pop-up-container");
    modal.setAttribute("class", "error-pop-up-container");
    modal.innerText = modalMessage;

    element.parentElement.parentElement.prepend(modal);

    console.error(data["error"]);
    console.log("ERROR_STACK_TRACE", data["stack"]);
}

function removeErrorPopUp(element){

    element.parentElement.parentElement.firstChild.setAttribute("style", "display: none;");

}

function flagReview(e){

    let data = new FormData();
    data.append("tableName", "car");
    data.append("carId", e.srcElement.dataset.carId);
    data.append("isFlagged", e.target.checked);

    var url = "http://localhost/car/flag";

    fetch(url, {
        method: "POST",
        body: data,
    })
    .then(response => response.json()
    .then(data => {

        if(response.status < 200 || response.status > 299){

            displayErrorPopUp(e.target, data);
        } else {

            removeErrorPopUp(e.target);
        }
    }))
    .catch(error => {
        console.log(error);
        console.error("ERROR", error);
    });

}

var elems = document.querySelectorAll(".flag-review");

for(let i = 0; i <= elems.length -1; i++){
	
	elems[i].addEventListener("change", flagReview);
}


