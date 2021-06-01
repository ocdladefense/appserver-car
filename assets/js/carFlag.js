// When the checkbox is checked a function is called that will update the record via a fetch requesst.


function flagReview(e){

    let data = new FormData();
    data.append("tableName", "car");
    data.append("carId", e.srcElement.dataset.carId);
    data.append("isFlagged", e.target.checked);

    var url = "http://localhost/car/flag";

    console.log(url);

    fetch(url, {
        method: "POST",
        body: data,
    }).then(response => {
        console.log(response.json());
    }).catch(error => {
        console.log(error);
        console.error("ERROR", error)
    });

}

var elems = document.querySelectorAll(".flag-review");

for(let i = 0; i <= elems.length -1; i++){
	
	elems[i].addEventListener("change", flagReview);
}


