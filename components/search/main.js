

// add the event listeners to anu searches that use the "judge-datalist"...datalist.
let judgeSearches = document.querySelectorAll("[data-datalist='judge-datalist']");

for(let i = 0; i < judgeSearches.length; i++){

    judgeSearches[i].addEventListener("keyup", minimumCharacterSearch);
}