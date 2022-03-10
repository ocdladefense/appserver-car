

let links = document.getElementsByClassName("delete-review");
let yearSummary = document.getElementById("year-summary");



if(yearSummary != null) yearSummary.addEventListener("change", submitFormSummaryYear);


for(let i = 0; i < links.length; i++){

    links[i].addEventListener("click", handleDelete);
}

function submitFormSummaryYear(){
    let selectedYear = document.getElementById("year-summary").value;

    let link = document.createElement("a");
    link.setAttribute("href", "/car/summary/" + selectedYear);
    link.click();
}

function submitForm(){

    $form = document.getElementById("car-search-form");
    $form.submit();
}


function handleDelete(e){

    e.preventDefault();
    e.stopPropagation();
    let confirmed = window.confirm("Are you sure you want to delete this review?") && window.confirm("Are you really sure?");

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

    let selectList = document.getElementsByName("subject")[0];

    let newOption = document.createElement("option");
    newOption.setAttribute("value", subject.toLowerCase());
    newOption.setAttribute("selected", true);
    newOption.innerText = toTitleCase(subject);

    selectList.appendChild(newOption);
}



function toTitleCase(str) {
    return str.replace(
      /\w\S*/g,
      function(txt) {
        return txt.charAt(0).toUpperCase() + txt.substr(1).toLowerCase();
      }
    );
  }


// add the event listeners to anu searches that use the "judge-datalist"...datalist.
let judgeSearches = document.querySelectorAll("[data-datalist='judge-datalist']");

for(let i = 0; i < judgeSearches.length; i++){

    judgeSearches[i].addEventListener("keyup", minimumCharacterSearch);
}


function minimumCharacterSearch(e){

    let inputElem = e.target;
    let dataListId = inputElem.getAttribute("data-datalist")
    let inputValue = e.target.value;

    if(inputValue.length >= 1){

        inputElem.setAttribute("list", dataListId);

    } else {

        inputElem.removeAttribute("list");
    }

}