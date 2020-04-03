
function readMore(ellipsis, moreText, btnText) {

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

