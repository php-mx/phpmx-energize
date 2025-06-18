_app.core.register("div[data-fragment]:not([static])", (element) => {
  _app.fragment(element.dataset.fragment, element.dataset.target ? document.getElementById(element.dataset.target) : element, element.dataset.mode);
});

_app.core.register("[href][data-fragment][data-target]", (element) => {
  element.addEventListener("click", (ev) => {
    ev.preventDefault();
    _app.fragment(element.dataset.fragment, element.dataset.target ? document.getElementById(element.dataset.target) : element, element.dataset.mode);
  });
});
