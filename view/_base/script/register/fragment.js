energize.core.register("div[data-fragment]:not([static])", (element) => {
  energize.fragment(
    element.dataset.fragment,
    element.dataset.target ? document.getElementById(element.dataset.target) : element,
    element.dataset.mode
  );
});

energize.core.register("[href][data-fragment][data-target]", (element) => {
  element.addEventListener("click", (ev) => {
    ev.preventDefault();
    energize.fragment(
      element.dataset.fragment,
      element.dataset.target ? document.getElementById(element.dataset.target) : element,
      element.dataset.mode
    );
  });
});
