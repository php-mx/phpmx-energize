_app.core.register("[href]:not([href=''])", (element) => {
  let url = new URL(element.href ?? element.getAttribute("href"), document.baseURI).href + "/";
  let href = window.location.href + "/";

  if (href.startsWith(url)) {
    element.classList.add("active-link");
    if (url == href) element.classList.add("current-link");
  } else {
    element.classList.remove("active-link");
    element.classList.remove("current-link");
  }
});
