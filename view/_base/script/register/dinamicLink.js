energize.core.register("[href]:not([static]):not([href=''])", (element) => {
  element.addEventListener("click", (event) => {
    event.preventDefault();
    let url = new URL(element.href ?? element.getAttribute("href"), document.baseURI).href;
    energize.go(url, document.baseURI);
  });
});
