energize.core.register("form[data-form-key]:not([static])", (element) => {
  element.addEventListener("submit", async (ev) => {
    ev.preventDefault();
    energize.submit(element);
  });
});
