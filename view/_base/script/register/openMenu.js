_app.core.register("ul.menu[data-open-url]", (element) => {
  let url = new URL(element.dataset.openUrl, document.baseURI).href + "/";
  let href = window.location.href + "/";
  href.startsWith(url) ? element.classList.add("open") : element.classList.remove("open");
});
