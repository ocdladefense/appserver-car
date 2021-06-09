

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

        let carId = e.srcElement.dataset.carId;

        let link = document.createElement("a");
        let href = "/car/delete/" + carId;
        console.log("href", href);
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