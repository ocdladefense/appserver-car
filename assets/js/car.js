

let links = document.getElementsByClassName("delete-review");

for(let i = 0; i < links.length; i++){

    links[i].addEventListener("click", handleDelete);
}

function submitForm(){

    document.getElementById("filter-form").submit();
}

function handleDelete(e){

    e.preventDefault();
    e.stopPropagation();
    let confirmed = window.confirm("Are you sure that you want to delete this case review?");

    if(confirmed) {

        // When you use an icon, you have to get the dataset from the parent element.
        let carId = e.srcElement.dataset.carId;

        if(carId == null) {

            carId = e.target.parentElement.dataset.carId;
        }

        if(carId == null) console.error("DELETE FAILED BECAUSE YOUR CAR ID IS NOT BEING SET.");

        let link = document.createElement("a");
        let href = "/car/delete/" + carId;
        link.setAttribute("href", href);
        link.click();
    }
}

function handleNewSubject(e){

    let subject = window.prompt("Enter a new primary subject");

    let selectList = document.getElementById("select-subject");

    let newOption = document.createElement("option");
    newOption.setAttribute("value", subject);
    newOption.setAttribute("selected", true);
    newOption.innerText = subject;

    selectList.appendChild(newOption);
}