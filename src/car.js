

function readMore() {
    const ellipsis = document.getElementById("dots");
    const moreText = document.getElementById("more");
    const btnText = document.getElementById("readMoreButton");

    if (ellipsis.style.display === "none") {
        ellipsis.style.display = "inline";
        btnText.innerHTML = "Read more";
        moreText.style.display = "none";
      } else {
        ellipsis.style.display = "none";
        btnText.innerHTML = "Read less";
        moreText.style.display = "inline";
      }
}

