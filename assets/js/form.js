




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