
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
        link.setAttribute("href", "/car/delete/" + carId);
        link.click();
    }
}